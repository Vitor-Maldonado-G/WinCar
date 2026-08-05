// pagina inativa 
<?php
session_start();
include 'includes/header.php';
?>

<div class="container my-5 py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            
            <div class="mb-4">
                <span class="badge bg-primary-subtle text-primary fs-6 px-3 py-2 rounded-pill">
                    Projeto Acadêmico
                </span>
            </div>

            <h1 class="fw-bold text-primary mb-3">Sobre o WinCar</h1>
            <p class="lead text-secondary mb-4">
                O **WinCar** é uma plataforma de agendamento de serviços automotivos desenvolvida para facilitar a gestão de lavagens, polimentos e manutenções veiculares de forma rápida e intuitiva.
            </p>

            <div class="card shadow-lg border-0 rounded-4 p-4 text-start bg-light mb-4">
                <div class="card-body">
                    <h5 class="fw-bold text-dark mb-3">⚠️ Aviso Importante:</h5>
                    <p class="text-muted mb-0">
                        Aviso: A empresa **WinCar** apresentada neste site é **estritamente fictícia**. Este sistema foi desenvolvido exclusivamente para fins didáticos e demonstrativos como **Trabalho de Conclusão de Curso (TCC)**. Nenhuma transação financeira real ou agendamento de serviço físico é realizado por este sistema.
                    </p>
                </div>
            </div>

            <a href="index.php" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm px-4">
                Voltar para a Tela Inicial
            </a>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>