<?php
// Inicia a sessão para resgatar o ID do cliente
session_start();

// Conexão com o banco de dados (ajuste o nome do arquivo se necessário)
require_once 'config/conexao.php';

// Função auxiliar para definir a mensagem estilizada e redirecionar
function emitirMensagem($mensagem, $tipo, $urlDestino) {
    $_SESSION['mensagem'] = $mensagem;
    $_SESSION['tipo_mensagem'] = $tipo;
    header("Location: $urlDestino");
    exit();
}

// 1. Verifica se o usuário está logado pegando o ID da sessão
$id_cliente = $_SESSION['usuario_id'] ?? null;

if (!$id_cliente) {
    emitirMensagem("Sua sessão expirou. Faça login novamente para agendar um serviço.", "warning", "login.php");
}

// Verifica se os dados vieram via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. Recebe e limpa os dados do formulário
    $modelo     = trim($_POST['modelo'] ?? '');
    $id_servico = intval($_POST['id_servico'] ?? 0);
    $data       = $_POST['data'] ?? '';
    $hora       = trim($_POST['hora'] ?? '');

    // Tratamento específico da placa: remove hífen e joga para maiúsculo
    $placa = trim($_POST['placa'] ?? '');
    $placa = strtoupper(str_replace('-', '', $placa));

    // Define o status padrão
    $status = 'Pendente';

    // 3. Validações de Segurança (Back-End)

    // Verifica se algum campo obrigatório veio vazio
    if (empty($modelo) || empty($placa) || empty($id_servico) || empty($data) || empty($hora)) {
        emitirMensagem("Todos os campos são obrigatórios.", "danger", "agendar.php");
    }

    // Validação de Placa (Padrão Antigo e Mercosul)
    if (!preg_match('/^[A-Z]{3}[0-9]{1}[A-Z0-9]{1}[0-9]{2}$/', $placa)) {
        emitirMensagem("Formato de placa inválido. Insira uma placa válida (ex: ABC1234 ou ABC1D23).", "danger", "agendar.php");
    }

    // Validação de Horário (Restrito entre 08:00 e 18:00)
    if ($hora < '08:00' || $hora > '18:00') {
        emitirMensagem("O horário de agendamento deve ser em horário comercial (08:00 às 18:00).", "danger", "agendar.php");
    }

    // 4. Inserção no Banco de Dados
    try {
        $sql = "INSERT INTO agendamento (id_cliente, id_servico, placa, modelo, data, hora, status) 
                VALUES (:id_cliente, :id_servico, :placa, :modelo, :data, :hora, :status)";

        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id_cliente', $id_cliente);
        $stmt->bindParam(':id_servico', $id_servico);
        $stmt->bindParam(':placa', $placa);
        $stmt->bindParam(':modelo', $modelo);
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':hora', $hora);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            // Sucesso: mensagem estilizada e redireciona para o painel
            emitirMensagem("Agendamento realizado com sucesso!", "success", "painelcliente.php");
        } else {
            // Falha na inserção
            emitirMensagem("Erro ao registrar o agendamento. Tente novamente.", "danger", "agendar.php");
        }

    } catch (PDOException $e) {
        // Não expõe o erro técnico do banco pro usuário final — fica só no log do servidor
        error_log("Erro ao inserir agendamento: " . $e->getMessage());
        emitirMensagem("Não foi possível registrar o agendamento. Tente novamente em instantes.", "danger", "agendar.php");
    }

} else {
    // Se alguém tentar acessar a página diretamente pela URL sem ser por POST
    emitirMensagem("Acesso inválido ao processador de agendamentos.", "warning", "agendar.php");
}
?>