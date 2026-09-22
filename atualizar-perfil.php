<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

if (($_SESSION['tipo_usuario'] ?? '') !== 'cliente' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

function voltarAoPainel($mensagem, $tipo) {
    $_SESSION['mensagem'] = $mensagem;
    $_SESSION['tipo_mensagem'] = $tipo;
    header('Location: painelcliente.php');
    exit();
}

$token = $_POST['csrf_token'] ?? '';
if (empty($_SESSION['csrf_perfil']) || !is_string($token) || !hash_equals($_SESSION['csrf_perfil'], $token)) {
    voltarAoPainel('Não foi possível validar a solicitação. Tente novamente.', 'danger');
}

$nome = $_POST['nome'] ?? '';
$telefone = $_POST['telefone'] ?? '';

if (!is_string($nome) || !is_string($telefone)) {
    voltarAoPainel('Informe nome e telefone válidos.', 'danger');
}

$nome = trim($nome);
$telefone = trim($telefone);

if (!preg_match('/^.{1,100}$/us', $nome)) {
    voltarAoPainel('Informe um nome válido com até 100 caracteres.', 'danger');
}

if (!preg_match('/^\(\d{2}\) \d{5}-\d{4}$/', $telefone)) {
    voltarAoPainel('Informe o telefone no formato (11) 99999-9999.', 'danger');
}

require_once 'config/conexao.php';

try {
    $stmt = $conexao->prepare(
        'UPDATE cliente SET nome = :nome, telefone = :telefone WHERE id_cliente = :id_cliente'
    );
    $stmt->execute([
        ':nome' => $nome,
        ':telefone' => $telefone,
        ':id_cliente' => $_SESSION['usuario_id'],
    ]);

    $_SESSION['usuario_nome'] = $nome;
    voltarAoPainel('Informações atualizadas com sucesso!', 'success');
} catch (PDOException $e) {
    error_log('Erro ao atualizar perfil: ' . $e->getMessage());
    voltarAoPainel('Não foi possível atualizar suas informações. Tente novamente.', 'danger');
}
