<?php
session_start();
require_once 'config/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recebe e limpa os dados do formulário
    $nome            = trim($_POST['nome'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $telefone        = trim($_POST['telefone'] ?? '');
    $senha           = $_POST['senha'] ?? '';
    $confirmarSenha  = $_POST['confirmar_senha'] ?? '';

    // Validação da senha no servidor (mín. 6 chars, 1 maiúscula, 1 número)
    if (strlen($senha) < 6 || !preg_match('/[A-Z]/', $senha) || !preg_match('/[0-9]/', $senha)) {
        $_SESSION['mensagem'] = "A senha deve ter no mínimo 6 caracteres, contendo pelo menos uma letra maiúscula e um número.";
        $_SESSION['tipo_mensagem'] = "danger";
        header("Location: cadastro.php");
        exit();
    }

    // Confere se a confirmação bate com a senha digitada
    if ($senha !== $confirmarSenha) {
        $_SESSION['mensagem'] = "As senhas não coincidem. Tente novamente.";
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
            $_SESSION['mensagem'] = "Cadastro realizado com sucesso! Faça login para continuar.";
            $_SESSION['tipo_mensagem'] = "success";
            header("Location: login.php");
            exit();
        }

    } catch (PDOException $e) {
        // Não expõe o erro técnico do banco pro usuário final — fica só no log do servidor
        error_log("Erro ao cadastrar cliente: " . $e->getMessage());

        // Detecta o caso mais comum (e-mail já cadastrado) pra dar uma mensagem útil
        if ($e->getCode() == 23000) {
            $_SESSION['mensagem'] = "Este e-mail já está cadastrado. Tente fazer login.";
            $_SESSION['tipo_mensagem'] = "warning";
        } else {
            $_SESSION['mensagem'] = "Não foi possível concluir o cadastro. Tente novamente em instantes.";
            $_SESSION['tipo_mensagem'] = "danger";
        }

        header("Location: cadastro.php");
        exit();
    }

} else {
    header("Location: cadastro.php");
    exit();
}