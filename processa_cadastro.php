<?php
session_start();
require_once 'config/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recebe e limpa os dados do formulário
    $nome     = trim($_POST['nome'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $senha    = $_POST['senha'] ?? '';

    // Validação da senha no servidor (mín. 6 chars, 1 maiúscula, 1 número)
    if (strlen($senha) < 6 || !preg_match('/[A-Z]/', $senha) || !preg_match('/[0-9]/', $senha)) {
        $_SESSION['mensagem'] = "A senha deve ter no mínimo 6 caracteres, contendo pelo menos uma letra maiúscula e um número.";
        $_SESSION['tipo_mensagem'] = "danger";
        header("Location: cadastro.php");
        exit();
    }

    // Criptografa a senha com segurança
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    try {
        // SQL para a tabela 'cliente'
        $sql = "INSERT INTO cliente (nome, email, telefone, senha, tipo_usuario) 
                VALUES (:nome, :email, :telefone, :senha, 'cliente')";
                
        $stmt = $conexao->prepare($sql);

        // Associa os parâmetros
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':senha', $senhaHash);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Cadastro realizado com sucesso! 🚗');
                    window.location.href = 'login.php';
                  </script>";
            exit();
        }

    } catch (PDOException $e) {
        echo "<h3>Erro ao cadastrar no banco de dados:</h3> " . htmlspecialchars($e->getMessage());
        echo "<br><br><a href='cadastro.php'>Voltar ao cadastro</a>";
    }

} else {
    header("Location: cadastro.php");
    exit();
}