<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

function responderJson($dados, $statusHttp = 200) {
    http_response_code($statusHttp);
    echo json_encode($dados);
    exit();
}

if (!isset($_SESSION['usuario_id'])) {
    responderJson(['erro' => 'Sessão expirada.'], 401);
}

if (($_SESSION['tipo_usuario'] ?? '') === 'admin') {
    responderJson(['erro' => 'Acesso não permitido.'], 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responderJson(['erro' => 'Método não permitido.'], 405);
}

$data = $_GET['data'] ?? '';

if (!is_string($data) || !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $data, $partes)) {
    responderJson(['erro' => 'Data inválida.'], 400);
}

if (!checkdate((int) $partes[2], (int) $partes[3], (int) $partes[1])) {
    responderJson(['erro' => 'Data inválida.'], 400);
}

require_once 'config/conexao.php';

try {
    $sql = "SELECT TIME_FORMAT(hora, '%H:%i') AS hora
            FROM agendamento
            WHERE data = :data AND status != 'Cancelado'
            ORDER BY hora";

    $stmt = $conexao->prepare($sql);
    $stmt->bindValue(':data', $data);
    $stmt->execute();

    $horariosOcupados = $stmt->fetchAll(PDO::FETCH_COLUMN);
    responderJson($horariosOcupados);
} catch (PDOException $e) {
    error_log('Erro ao consultar horários disponíveis: ' . $e->getMessage());
    responderJson(['erro' => 'Não foi possível consultar os horários.'], 500);
}
