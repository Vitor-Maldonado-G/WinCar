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
    // Confere o status ATUAL no banco antes de alterar — a trava do front pode
    // ser burlada mandando um POST direto pra este arquivo.
    $sqlAtual = "SELECT status FROM agendamento WHERE id_agendamento = :id_agendamento";
    $stmtAtual = $conexao->prepare($sqlAtual);
    $stmtAtual->bindParam(':id_agendamento', $id_agendamento, PDO::PARAM_INT);
    $stmtAtual->execute();
    $agendamentoAtual = $stmtAtual->fetch(PDO::FETCH_ASSOC);

    if (!$agendamentoAtual) {
        $_SESSION['mensagem'] = 'Agendamento não encontrado.';
        $_SESSION['tipo_mensagem'] = 'danger';
        header("Location: painel-admin.php");
        exit();
    }

    if ($agendamentoAtual['status'] === 'Cancelado') {
        $_SESSION['mensagem'] = 'Este agendamento já foi cancelado e não pode ser alterado.';
        $_SESSION['tipo_mensagem'] = 'warning';
        header("Location: painel-admin.php");
        exit();
    }

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