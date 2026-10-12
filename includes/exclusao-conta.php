<?php

function excluirContaCliente(PDO $conexao, $idCliente, $senha) {
    if (!is_string($senha) || $senha === '') {
        return false;
    }

    $conexao->beginTransaction();
    try {
        $stmt = $conexao->prepare('SELECT senha, tipo_usuario FROM cliente WHERE id_cliente = :id FOR UPDATE');
        $stmt->execute([':id' => $idCliente]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$cliente || $cliente['tipo_usuario'] !== 'cliente' || !password_verify($senha, $cliente['senha'])) {
            $conexao->rollBack();
            return false;
        }

        // Apaga os dados dependentes antes da conta, sem deixar uma exclusao pela metade.
        $stmt = $conexao->prepare('DELETE FROM agendamento WHERE id_cliente = :id');
        $stmt->execute([':id' => $idCliente]);
        $stmt = $conexao->prepare('DELETE FROM veiculo WHERE id_cliente = :id');
        $stmt->execute([':id' => $idCliente]);
        $stmt = $conexao->prepare('DELETE FROM recuperacao_senha WHERE id_cliente = :id');
        $stmt->execute([':id' => $idCliente]);
        $stmt = $conexao->prepare('DELETE FROM cliente WHERE id_cliente = :id');
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
