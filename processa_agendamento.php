<?php
// Inicia a sessão para resgatar o ID do cliente
session_start();

// Conexão com o banco de dados (ajuste o nome do arquivo se necessário)
require_once 'config/conexao.php';

// Função auxiliar para disparar os alertas em JS e redirecionar
function emitirAlerta($mensagem, $urlDestino = null) {
    echo "<script>";
    echo "alert('$mensagem');";
    if ($urlDestino) {
        echo "window.location.href = '$urlDestino';";
    } else {
        echo "window.history.back();"; // Volta para agendar.php mantendo os dados
    }
    echo "</script>";
    exit();
}

// 1. Verifica se o usuário está logado pegando o ID da sessão
$id_cliente = $_SESSION['usuario_id'] ?? $_SESSION['usuario_id'] ?? null;

if (!$id_cliente) {
    emitirAlerta("Erro de autenticação! Faça login para agendar um serviço.", "login.php");
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
        emitirAlerta("Erro: Todos os campos são obrigatórios!");
    }

    // Validação de Placa (Padrão Antigo e Mercosul)
    if (!preg_match('/^[A-Z]{3}[0-9]{1}[A-Z0-9]{1}[0-9]{2}$/', $placa)) {
        emitirAlerta("Erro: Formato de placa inválido! Insira uma placa válida (ex: ABC1234 ou ABC1D23).");
    }

    // Validação de Horário (Restrito entre 08:00 e 18:00)
    if ($hora < '08:00' || $hora > '18:00') {
        emitirAlerta("Erro: O horário de agendamento deve ser apenas em horário comercial (08:00 às 18:00).");
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
            // Sucesso: Alerta e redireciona para o painel
            emitirAlerta("Agendamento realizado com sucesso!", "painelcliente.php");
        } else {
            // Falha na inserção
            emitirAlerta("Erro ao registrar o agendamento. Tente novamente.");
        }

    } catch (PDOException $e) {
        // Em um ambiente de produção real, é melhor não mostrar o erro do PDO direto pro usuário,
        // mas para o TCC isso ajuda a debugar se houver problema na estrutura da tabela.
        emitirAlerta("Erro no banco de dados: " . $e->getMessage());
    }

} else {
    // Se alguém tentar acessar a página diretamente pela URL sem ser por POST
    emitirAlerta("Acesso inválido ao processador de agendamentos.", "agendar.php");
}
?>