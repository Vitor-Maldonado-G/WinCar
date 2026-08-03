<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WinCar - Sistema de Agendamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
    
    <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
      <img src="assets/img/logowincar.png" alt="WinCar Logo" height="40" class="me-2">
      WinCar
    </a>

   <div class="d-flex align-items-center gap-3 ms-auto">

    <?php if (isset($_SESSION['usuario_nome'])): ?>
        
        <?php 
            $nomeCompleto = $_SESSION['usuario_nome'];
            $primeiroNome = explode(' ', trim($nomeCompleto))[0];
        ?>

        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="assets/img/user.png" alt="Perfil" width="32" height="32" class="rounded-circle me-2 border border-white">
                <span class="fw-semibold">Olá, <?php echo htmlspecialchars($primeiroNome); ?>!</span>
            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser">
                <li><a class="dropdown-item" href="painel-cliente.php">Meus Agendamentos</a></li>
                <li><a class="dropdown-item" href="agendar.php">Novo Agendamento</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger fw-bold" href="logout.php">Sair</a></li>
            </ul>
        </div>

    <?php else: ?>

        <a href="login.php" class="btn btn-outline-light me-2">Entrar</a>
        <a href="cadastro.php" class="btn btn-primary fw-bold">Cadastrar</a>

    <?php endif; ?>

    </div>  

  </div>
</nav>
<div class="container my-auto flex-grow-1 d-flex flex-column justify-content-center py-4">