<?php
session_start();
include 'includes/header.php';
?>

<main class="container my-5 py-3">
    <header class="text-center mx-auto mb-5" style="max-width: 800px;">
        <span class="badge bg-primary-subtle text-primary px-3 py-2 mb-3">Projeto acadêmico</span>
        <h1 class="fw-bold text-primary mb-3">Sobre o WinCar</h1>
        <p class="lead text-secondary mb-0">
            O <strong>WinCar</strong> é um sistema de agendamento de serviços automotivos criado para aproximar clientes e estabelecimentos de forma simples e organizada.
        </p>
    </header>

    <section class="row align-items-center g-4 mb-5 pb-5 border-bottom" aria-labelledby="titulo-objetivo">
        <div class="col-12 col-lg-7">
            <h2 id="titulo-objetivo" class="h3 fw-bold text-dark mb-3">Nosso objetivo</h2>
            <p class="text-secondary mb-0">
                Facilitar a consulta de serviços, o agendamento de horários e o acompanhamento dos atendimentos. O sistema também oferece ao administrador uma visão centralizada dos agendamentos realizados.
            </p>
        </div>
        <div class="col-12 col-lg-5">
            <div class="bg-primary text-white p-4 rounded-3">
                <i class="bi bi-car-front-fill fs-1 d-block mb-2" aria-hidden="true"></i>
                <p class="fw-semibold mb-0">Uma solução prática para organizar serviços e horários automotivos.</p>
            </div>
        </div>
    </section>

    <section class="mb-5 pb-5 border-bottom" aria-labelledby="titulo-funcionalidades">
        <div class="text-center mb-4">
            <h2 id="titulo-funcionalidades" class="h3 fw-bold text-primary mb-2">Principais funcionalidades</h2>
            <p class="text-secondary mb-0">O essencial para realizar e acompanhar um atendimento.</p>
        </div>

        <div class="row g-4">
            <div class="col-12 col-md-4">
                <div class="card card-pagamento h-100 p-4 text-center rounded-3">
                    <i class="bi bi-search fs-2 text-primary mb-3" aria-hidden="true"></i>
                    <h3 class="h5 fw-bold">Consultar serviços</h3>
                    <p class="text-secondary mb-0">Visualizar opções, preços e duração estimada.</p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card card-pagamento h-100 p-4 text-center rounded-3">
                    <i class="bi bi-calendar-check fs-2 text-primary mb-3" aria-hidden="true"></i>
                    <h3 class="h5 fw-bold">Fazer agendamentos</h3>
                    <p class="text-secondary mb-0">Escolher o serviço, a data e o horário desejados.</p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card card-pagamento h-100 p-4 text-center rounded-3">
                    <i class="bi bi-list-check fs-2 text-primary mb-3" aria-hidden="true"></i>
                    <h3 class="h5 fw-bold">Acompanhar atendimentos</h3>
                    <p class="text-secondary mb-0">Consultar o histórico e o status de cada agendamento.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="row g-4 align-items-start mb-5" aria-labelledby="titulo-tecnologias">
        <div class="col-12 col-lg-6">
            <h2 id="titulo-tecnologias" class="h3 fw-bold text-dark mb-3">Tecnologias utilizadas</h2>
            <p class="text-secondary mb-3">O projeto foi desenvolvido com tecnologias estudadas durante o curso:</p>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-primary fs-6 px-3 py-2">HTML</span>
                <span class="badge bg-primary fs-6 px-3 py-2">CSS</span>
                <span class="badge bg-primary fs-6 px-3 py-2">JavaScript</span>
                <span class="badge bg-primary fs-6 px-3 py-2">Bootstrap</span>
                <span class="badge bg-primary fs-6 px-3 py-2">PHP</span>
                <span class="badge bg-primary fs-6 px-3 py-2">MySQL</span>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="alert alert-primary mb-0" role="note">
                <h2 class="h5 fw-bold mb-2"><i class="bi bi-mortarboard-fill me-2" aria-hidden="true"></i>Projeto de TCC</h2>
                <p class="mb-0">
                    A empresa WinCar é fictícia. Este sistema foi desenvolvido exclusivamente para fins acadêmicos e demonstrativos, sem realizar transações financeiras ou atendimentos reais.
                </p>
            </div>
        </div>
    </section>

    <div class="text-center">
        <a href="index.php" class="btn btn-primary btn-lg fw-bold px-4">
            <i class="bi bi-house-door me-2" aria-hidden="true"></i>Voltar para a página inicial
        </a>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
