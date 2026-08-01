<?php
require_once 'config/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recebe os dados do formulário
    $nome  = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $placa = trim($_POST['placa']); // Caso queira guardar a placa ou telefone
    $senha = $_POST['senha'];

    // Criptografa a senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    try {
        // SQL ajustada para a tabela 'cliente' do seu phpMyAdmin
        $sql = "INSERT INTO cliente (nome, email, telefone, senha, tipo_usuario) 
                VALUES (:nome, :email, :telefone, :senha, 'cliente')";
                
        $stmt = $conexao->prepare($sql);

        // Associa as variáveis
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefone', $placa); // usando o campo placa como telefone/identificador por enquanto
        $stmt->bindParam(':senha', $senhaHash);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Cadastro realizado com sucesso! 🚗');
                    window.location.href = 'login.php';
                  </script>";
            exit();
        }

    } catch (PDOException $e) {
        echo "<h3>Erro ao cadastrar no banco de dados:</h3> " . $e->getMessage();
        echo "<br><br><a href='cadastro.php'>Voltar ao cadastro</a>";
    }

} else {
    header("Location: cadastro.php");
    exit();
}