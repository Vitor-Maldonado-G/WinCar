<?php

session_start();


require_once 'cadastro.php'; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    try {
       
        $sql = "SELECT id_cliente, nome, senha FROM cliente WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

      
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            
            
            $_SESSION['id_cliente'] = $usuario['id_cliente'];
            $_SESSION['nome'] = $usuario['nome'];
            
            
            header("Location: painel.php");
            exit();
            
        } else {
            
            header("Location: login.php?erro=credenciais");
            exit();
        }

    } catch (PDOException $e) {
        
        die("Erro no banco de dados: " . $e->getMessage());
    }
    
 } else {
    
    header("Location: login.php");
    exit();
 }
?>