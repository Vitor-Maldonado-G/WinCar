<?php

function redirecionarRecuperacao($mensagem, $tipo, $destino) {
    $_SESSION['mensagem'] = $mensagem;
    $_SESSION['tipo_mensagem'] = $tipo;
    header('Location: ' . $destino);
    exit();
}

function csrfRecuperacaoValido($token) {
    return is_string($token) && !empty($_SESSION['csrf_recuperacao'])
        && hash_equals($_SESSION['csrf_recuperacao'], $token);
}

function tokenRecuperacaoValido($token) {
    return is_string($token) && preg_match('/\A[a-f0-9]{64}\z/', $token) === 1;
}

function criarRecuperacao(PDO $conexao, $email) {
    $conexao->beginTransaction();
    try {
        // Bloqueia a conta durante a solicitacao para respeitar o intervalo entre envios.
        $stmt = $conexao->prepare('SELECT id_cliente, email FROM cliente WHERE email = :email FOR UPDATE');
        $stmt->execute([':email' => $email]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$cliente) {
            $conexao->rollBack();
            return null;
        }

        $stmt = $conexao->prepare('SELECT id_cliente FROM recuperacao_senha
            WHERE id_cliente = :id AND solicitado_em > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 MINUTE)');
        $stmt->execute([':id' => $cliente['id_cliente']]);
        if ($stmt->fetchColumn()) {
            $conexao->rollBack();
            return null;
        }

        $token = bin2hex(random_bytes(32));
        $stmt = $conexao->prepare('INSERT INTO recuperacao_senha
            (id_cliente, token_hash, solicitado_em, expira_em, usado_em)
            VALUES (:id, :hash, UTC_TIMESTAMP(), DATE_ADD(UTC_TIMESTAMP(), INTERVAL 30 MINUTE), NULL)
            ON DUPLICATE KEY UPDATE token_hash = VALUES(token_hash),
                solicitado_em = VALUES(solicitado_em), expira_em = VALUES(expira_em), usado_em = NULL');
        $stmt->execute([':id' => $cliente['id_cliente'], ':hash' => hash('sha256', $token)]);
        $conexao->commit();
        return ['email' => $cliente['email'], 'token' => $token];
    } catch (Throwable $e) {
        if ($conexao->inTransaction()) {
            $conexao->rollBack();
        }
        throw $e;
    }
}

function buscarRecuperacao(PDO $conexao, $token) {
    if (!tokenRecuperacaoValido($token)) {
        return false;
    }
    $stmt = $conexao->prepare('SELECT id_cliente FROM recuperacao_senha
        WHERE token_hash = :hash AND usado_em IS NULL AND expira_em > UTC_TIMESTAMP()');
    $stmt->execute([':hash' => hash('sha256', $token)]);
    return $stmt->fetchColumn();
}

function erroNovaSenha($senha, $confirmacao) {
    if (!is_string($senha) || !is_string($confirmacao)) {
        return 'Preencha a senha e sua confirmação.';
    }
    if (strlen($senha) > 72 || str_contains($senha, "\0")) {
        return 'Senha muito longa ou inválida. Escolha outra senha.';
    }
    if (strlen($senha) < 6 || !preg_match('/[A-Z]/', $senha) || !preg_match('/[0-9]/', $senha)) {
        return 'A senha deve ter no mínimo 6 caracteres, uma letra maiúscula e um número.';
    }
    if ($senha !== $confirmacao) {
        return 'As senhas não coincidem.';
    }
    return null;
}

function redefinirSenha(PDO $conexao, $token, $senha) {
    if (!tokenRecuperacaoValido($token) || erroNovaSenha($senha, $senha) !== null) {
        return false;
    }
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    $idCliente = buscarRecuperacao($conexao, $token);
    if (!$idCliente) {
        return false;
    }
    $conexao->beginTransaction();
    try {
        // Mantem a mesma ordem de bloqueio usada ao solicitar um link.
        $stmt = $conexao->prepare('SELECT id_cliente FROM cliente WHERE id_cliente = :id FOR UPDATE');
        $stmt->execute([':id' => $idCliente]);
        // Consome o link na mesma transacao da senha: dois envios nao podem reutiliza-lo.
        $stmt = $conexao->prepare('SELECT id_cliente FROM recuperacao_senha
            WHERE token_hash = :hash AND usado_em IS NULL AND expira_em > UTC_TIMESTAMP() FOR UPDATE');
        $stmt->execute([':hash' => hash('sha256', $token)]);
        $idCliente = $stmt->fetchColumn();
        if (!$idCliente) {
            $conexao->rollBack();
            return false;
        }

        $stmt = $conexao->prepare('UPDATE cliente SET senha = :senha WHERE id_cliente = :id');
        $stmt->execute([':senha' => $senhaHash, ':id' => $idCliente]);
        $stmt = $conexao->prepare('UPDATE recuperacao_senha SET usado_em = UTC_TIMESTAMP() WHERE id_cliente = :id');
        $stmt->execute([':id' => $idCliente]);
        $conexao->commit();
        return true;
    } catch (Throwable $e) {
        if ($conexao->inTransaction()) {
            $conexao->rollBack();
        }
        throw $e;
    }
}
