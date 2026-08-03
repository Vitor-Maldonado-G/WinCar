<?php
session_start();


if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit(); 
}

$nome_cliente = isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Cliente';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Cliente - WinCar</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 0; }
        .cabecalho { background-color: #2c3e50; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .cabecalho a { color: #e74c3c; text-decoration: none; font-weight: bold; }
        .container { max-width: 800px; margin: 40px auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .btn { display: inline-block; background-color: #2980b9; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .btn:hover { background-color: #3498db; }
    </style>
</head>
<body>

    <header class="cabecalho">
        <h2>WinCar</h2>
        <a href="logout.php">Sair</a>
    </header>

    <div class="container">
        <h1>Bem-vindo, <?php echo htmlspecialchars($nome_cliente); ?>!</h1>
        <p>Este é o seu painel de controle. Aqui você poderá gerenciar os serviços do seu veículo.</p>
        
        <hr>

        <h3>Seus Agendamentos</h3>
        <p>Você ainda não possui serviços agendados.</p>

        <a href="agendamento.php" class="btn">Fazer Novo Agendamento</a>
    </div>

</body>
</html>