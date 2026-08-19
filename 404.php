<?php
session_start();
include 'includes/header.php';
?>

<div class="container my-5 py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">

            <img src="assets/img/logowincar.png" alt="WinCar Logo" height="80" class="mb-4 opacity-75">

            <h1 class="fw-bold text-primary mb-2" style="font-size: 4rem;">404</h1>
            <h3 class="fw-bold mb-3">Página não encontrada</h3>
            <p class="text-muted fs-5 mb-4">
                A página que você está procurando não existe ou foi movida.
            </p>

            <a href="index.php" class="btn btn-primary btn-lg rounded-pill fw-bold px-4">
                Voltar para a Tela Inicial
            </a>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>