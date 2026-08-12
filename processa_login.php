<?php

session_start();


require_once 'config/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    try {

        $sql = "SELECT id_cliente, nome, senha, tipo_usuario FROM cliente WHERE email = :email";
        
        
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
    $_SESSION['usuario_id']   = $usuario['id_cliente'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

    if ($_SESSION['tipo_usuario'] === 'admin') {
        header("Location: admin/painel-admin.php");
    } else {
        header("Location: painelcliente.php");
    }
    exit();
} else {
    $_SESSION['mensagem'] = "E-mail ou senha incorretos.";
    $_SESSION['tipo_mensagem'] = "danger";
    header("Location: login.php");
    exit();
}

    } catch (PDOException $e) {
        error_log("Erro no login: " . $e->getMessage());
        $_SESSION['mensagem'] = "Não foi possível processar o login. Tente novamente em instantes.";
        $_SESSION['tipo_mensagem'] = "danger";
        header("Location: login.php");
        exit();
    }
}
    
  else {
    
    header("Location: index.php");
    exit();
 }
?>