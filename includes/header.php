<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<script>
    (function() {
        if (localStorage.getItem('wincar-tema') === 'dark') {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
        }
    })();
</script>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WinCar - Sistema de Agendamento</title>
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // Se o CDN do Bootstrap falhar (sem internet, por exemplo), carrega a cópia local
        (function() {
            var testEl = document.createElement('div');
            testEl.className = 'd-none';
            document.documentElement.appendChild(testEl);
            var cdnFuncionou = window.getComputedStyle(testEl).display === 'none';
            document.documentElement.removeChild(testEl);
            if (!cdnFuncionou) {
                document.write('<link rel="stylesheet" href="assets/css/bootstrap.min.css">');
            }
        })();
    </script>
    <link href="assets/css/estilo.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

</head>

<body class="d-flex flex-column min-vh-100 bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid px-4 d-flex justify-content-between align-items-center">

    <span class="navbar-brand fw-bold d-flex align-items-center">
      <img src="assets/img/logowincar.png" alt="WinCar Logo" height="40" class="me-2">
      WinCar
    </span>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarWinCar" aria-controls="navbarWinCar" aria-expanded="false" aria-label="Alternar navegação">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarWinCar">

      <ul class="navbar-nav flex-lg-row gap-lg-3 mx-lg-4">
        <li class="nav-item">
          <a class="nav-link text-white fw-bold fs-5" href="index.php">Tela inicial</a>
        </li>
        <?php if (isset($_SESSION['usuario_nome'])): ?>
        <li class="nav-item">
          <a class="nav-link text-white fw-bold fs-5" href="painelcliente.php">Meus Agendamentos</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white fw-bold fs-5" href="agendar.php">Novo Agendamento</a>
        </li>
        <?php endif; ?>
        <li class="nav-item">
          <a class="nav-link text-white fw-bold fs-5" href="sobrenos.php">Sobre nós</a>
        </li>
      </ul>

      <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-3 ms-lg-auto mt-3 mt-lg-0">

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
                    <li>
                        <button class="dropdown-item d-flex align-items-center gap-2" id="toggleTema" type="button">
                            <i class="bi bi-moon-stars"></i> Alternar tema
                        </button>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger fw-bold" href="logout.php">Sair</a></li>
                </ul>
            </div>

        <?php else: ?>

            <button class="btn btn-outline-light btn-sm" id="toggleTema" type="button" title="Alternar tema">
                <i class="bi bi-moon-stars"></i>
            </button>

            <a href="login.php" class="btn btn-outline-light me-2">Entrar</a>
            <a href="cadastro.php" class="btn btn-primary fw-bold">Cadastrar</a>

        <?php endif; ?>

      </div>

    </div>

  </div>
</nav>
<div class="container my-auto flex-grow-1 d-flex flex-column justify-content-center py-4">