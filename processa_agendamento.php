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

    if (empty($modelo) || empty($placa) || empty($id_servico) || empty($data) || empty($hora)) {
        emitirMensagem("Todos os campos são obrigatórios.", "danger", "agendar.php");
    }

    if (!preg_match('/^[A-Z]{3}[0-9]{1}[A-Z0-9]{1}[0-9]{2}$/', $placa)) {
        emitirMensagem("Formato de placa inválido. Insira uma placa válida (ex: ABC1234 ou ABC1D23).", "danger", "agendar.php");
    }

    if ($hora < '08:00' || $hora > '18:00') {
        emitirMensagem("O horário de agendamento deve ser em horário comercial (08:00 às 18:00).", "danger", "agendar.php");
    }

    try {
        // 4. Confere se o horário já está ocupado (agendamento ATIVO, não cancelado)
        $sqlCheck = "SELECT COUNT(*) FROM agendamento WHERE data = :data AND hora = :hora AND status != 'Cancelado'";
        $stmtCheck = $conexao->prepare($sqlCheck);
        $stmtCheck->bindParam(':data', $data);
        $stmtCheck->bindParam(':hora', $hora);
        $stmtCheck->execute();

        if ($stmtCheck->fetchColumn() > 0) {
            emitirMensagem("Este horário já está ocupado, escolha outro.", "danger", "agendar.php");
        }

        // 5. Confere/gerencia o veículo pela placa antes de agendar
        $sqlVeiculo = "SELECT id_cliente, modelo FROM veiculo WHERE placa = :placa";
        $stmtVeiculo = $conexao->prepare($sqlVeiculo);
        $stmtVeiculo->bindParam(':placa', $placa);
        $stmtVeiculo->execute();
        $veiculoExistente = $stmtVeiculo->fetch(PDO::FETCH_ASSOC);

        if ($veiculoExistente) {
            // Placa já existe no sistema — confere de quem é
            if ($veiculoExistente['id_cliente'] != $id_cliente) {
                emitirMensagem("Esta placa já está cadastrada em outra conta. Se o veículo mudou de dono, entre em contato com o suporte.", "danger", "agendar.php");
            }

            if ($veiculoExistente['modelo'] !== $modelo) {
                emitirMensagem("Você já tem essa placa cadastrada como \"" . htmlspecialchars($veiculoExistente['modelo']) . "\". Selecione o modelo correto ou entre em contato com o suporte para corrigir.", "danger", "agendar.php");
            }
            // Placa já é do próprio cliente com o mesmo modelo — segue normal
        } else {
            // Placa nova — cadastra o veículo vinculado a este cliente
            $sqlNovoVeiculo = "INSERT INTO veiculo (id_cliente, placa, modelo) VALUES (:id_cliente, :placa, :modelo)";
            $stmtNovoVeiculo = $conexao->prepare($sqlNovoVeiculo);
            $stmtNovoVeiculo->bindParam(':id_cliente', $id_cliente);
            $stmtNovoVeiculo->bindParam(':placa', $placa);
            $stmtNovoVeiculo->bindParam(':modelo', $modelo);
            $stmtNovoVeiculo->execute();
        }

        // 6. Inserção do agendamento (continua igual a antes)
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
            emitirMensagem("Agendamento realizado com sucesso!", "success", "painelcliente.php");
        } else {
            emitirMensagem("Erro ao registrar o agendamento. Tente novamente.", "danger", "agendar.php");
        }

    } catch (PDOException $e) {
        error_log("Erro ao inserir agendamento: " . $e->getMessage());
        emitirMensagem("Não foi possível registrar o agendamento. Tente novamente em instantes.", "danger", "agendar.php");
    }

} else {
    emitirMensagem("Acesso inválido ao processador de agendamentos.", "warning", "agendar.php");
}
?>