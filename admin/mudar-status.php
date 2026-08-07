<?php
require_once __DIR__ . '/index.php'; // só admin logado pode mudar status
require_once '../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: painel-admin.php");
    exit();
}

$id_agendamento = $_POST['id_agendamento'] ?? null;
$novo_status    = $_POST['novo_status'] ?? null;

$status_permitidos = ['Pendente', 'Confirmado', 'Concluido', 'Cancelado'];

if (!$id_agendamento || !in_array($novo_status, $status_permitidos, true)) {
    $_SESSION['mensagem'] = 'Status inválido.';
    $_SESSION['tipo_mensagem'] = 'danger';
    header("Location: painel-admin.php");
    exit();
}

try {
    $sql = "UPDATE agendamento SET status = :status WHERE id_agendamento = :id_agendamento";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':status', $novo_status);
    $stmt->bindParam(':id_agendamento', $id_agendamento, PDO::PARAM_INT);
    $stmt->execute();

    $_SESSION['mensagem'] = 'Status do agendamento atualizado com sucesso!';
    $_SESSION['tipo_mensagem'] = 'success';
} catch (PDOException $e) {
    $_SESSION['mensagem'] = 'Erro ao atualizar status.';
    $_SESSION['tipo_mensagem'] = 'danger';
}

header("Location: painel-admin.php");
exit();
?>