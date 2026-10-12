<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
if (($_SESSION['tipo_usuario'] ?? '') !== 'cliente') {
    header('Location: index.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: painelcliente.php');
    exit();
}

function voltarDaExclusao($mensagem) {
    $_SESSION['mensagem'] = $mensagem;
    $_SESSION['tipo_mensagem'] = 'danger';
    header('Location: painelcliente.php');
    exit();
}

$token = $_POST['csrf_token'] ?? null;
if (!is_string($token) || empty($_SESSION['csrf_excluir_conta'])
    || !hash_equals($_SESSION['csrf_excluir_conta'], $token)) {
    voltarDaExclusao('Não foi possível validar a solicitação. Recarregue a página e tente novamente.');
}
if (($_POST['confirmar_exclusao'] ?? '') !== '1') {
    voltarDaExclusao('Confirme que deseja excluir sua conta e seus dados.');
}

require_once 'config/conexao.php';
require_once 'includes/exclusao-conta.php';

try {
    if (!excluirContaCliente($conexao, $_SESSION['usuario_id'], $_POST['senha'] ?? null)) {
        voltarDaExclusao('Não foi possível excluir a conta. Confira sua senha atual.');
    }
} catch (Throwable $e) {
    error_log('WinCar: falha ao excluir conta. A operacao foi revertida; verifique as tabelas e a conexao.');
    voltarDaExclusao('Não foi possível excluir sua conta. Nenhum dado foi apagado. Tente novamente mais tarde.');
}

// Remove a autenticacao e troca a sessao, mantendo apenas a mensagem de confirmacao.
$_SESSION = [];
session_regenerate_id(true);
$_SESSION['mensagem'] = 'Sua conta e os dados associados foram excluídos. Os horários reservados foram liberados.';
$_SESSION['tipo_mensagem'] = 'success';
header('Location: login.php');
exit();
