<?php

session_start();


require_once 'config/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    try {

        $sql = "SELECT id_cliente, nome, senha FROM cliente WHERE email = :email";
        
        
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
    $_SESSION['usuario_id']   = $usuario['id_cliente'];
    $_SESSION['usuario_nome'] = $usuario['nome'];

    header("Location: painelcliente.php");
    exit();
} else {
    echo "<script>
            alert('E-mail ou senha incorretos!');
            window.location.href = 'login.php';
          </script>";
    exit();
}

    } catch (PDOException $e) {
        echo "Erro no login: " . $e->getMessage();
    }
}
    
  else {
    
    header("Location: index.php");
    exit();
 }
?>