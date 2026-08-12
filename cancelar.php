<?php
session_start();
require_once 'config/conexao.php';

// 1. Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtém o ID do cliente logado na sessão
$id_cliente = $_SESSION['usuario_id'] ?? $_SESSION['usuario_id'];

// 2. Verifica se o ID do agendamento foi passado via URL (GET)
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_agendamento = intval($_GET['id']);

    try {
        // 3. Atualiza o status para 'Cancelado' GARANTINDO que o agendamento é do cliente logado
        $sql = "UPDATE agendamento 
                SET status = 'Cancelado' 
                WHERE id_agendamento = :id_agendamento AND id_cliente = :id_cliente";
        
        $stmt =$conexao->prepare($sql);$stmt->bindValue(':id_agendamento', $id_agendamento, PDO::PARAM_INT);$stmt->bindValue(':id_cliente', $id_cliente, PDO::PARAM_INT);$stmt->execute();

        if ($stmt->rowCount() > 0) {$_SESSION['mensagem'] = "Agendamento cancelado com sucesso!";
            $_SESSION['tipo_mensagem'] = "success";
        } else {
            $_SESSION['mensagem'] = "Não foi possível cancelar o agendamento ou ele não foi encontrado.";
            $_SESSION['tipo_mensagem'] = "danger";
        }

    } catch (PDOException $e) {
        error_log("Erro ao cancelar agendamento: " . $e->getMessage());
        $_SESSION['mensagem'] = "Não foi possível cancelar o agendamento. Tente novamente em instantes.";
        $_SESSION['tipo_mensagem'] = "danger";
    }
} else {
    $_SESSION['mensagem'] = "Agendamento inválido.";
    $_SESSION['tipo_mensagem'] = "warning";
}

// Redireciona de volta para o painel do cliente
header("Location: painelcliente.php");
exit();