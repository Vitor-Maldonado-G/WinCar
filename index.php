<?php
session_start();

// Conexão com o banco de dados
require_once 'config/conexao.php';

// Busca a lista de serviços ativos no banco
try {
    $stmt = $conexao->query("SELECT * FROM servico");
    $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $servicos = [];
}

include 'includes/header.php';
?>


<div class="text-center mb-4 container">
    <h1 class="display-4 fw-bold text-primary">Bem-vindo ao WinCar!</h1>
    <p class="lead text-muted">O melhor sistema de agendamento para o seu veículo.</p>
</div>

<div id="carrosselWinCar" class="carousel slide shadow-lg mb-4" data-bs-ride="carousel">
    
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#carrosselWinCar" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#carrosselWinCar" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#carrosselWinCar" data-bs-slide-to="2"></button>
    </div>

    <div class="carousel-inner">
        
        <div class="carousel-item active">
            <img src="https://images.unsplash.com/photo-1520340356584-f9917d1eea6f?auto=format&fit=crop&w=1600&q=80" class="d-block w-100" alt="Lava-rápido profissional" style="height: 450px; object-fit: cover;">
            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3 mb-3">
                <h3 class="fw-bold">Lavagem Completa e Detalhada</h3>
                <p class="fs-5">Cuidamos do seu veículo com os melhores produtos.</p>
            </div>
        </div>

        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1601362840469-51e4d8d58785?auto=format&fit=crop&w=1600&q=80" class="d-block w-100" alt="Polimento e Espelhamento" style="height: 450px; object-fit: cover;">
            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3 mb-3">
                <h3 class="fw-bold">Polimento e Higienização</h3>
                <p class="fs-5">Seu carro novo, por dentro e por fora.</p>
            </div>
        </div>

        <div class="carousel-item">
            <img src="assets/img/lavando1.jpg" class="d-block w-100" alt="Agendamento Prático" style="height: 450px; object-fit: cover;">
            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3 mb-3">
                <h3 class="fw-bold">Agendamento Rápido</h3>
                <p class="fs-5">Escolha o melhor horario, que cabe na sua agenda.</p>
            </div>
        </div>

    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#carrosselWinCar" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
        <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carrosselWinCar" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
        <span class="visually-hidden">Próximo</span>
    </button>

</div>

<section class="container my-5">
    
    <div class="text-center mb-5">
        <h2 class="fw-bold text-primary">Nossos Serviços</h2>
        <p class="text-muted fs-5">Confira o que oferecemos para deixar seu veículo novo em folha</p>
    </div>

    <div class="row g-4">
        
        <?php if (!empty($servicos)): ?>
            <?php foreach ($servicos as $servico): ?>
                <div class="col-12 col-md-4">
                    <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden card-servico">
                        
                        <img 
                            src="assets/img/<?php echo !empty($servico['imagem']) ? $servico['imagem'] : 'servico-padrao.jpg'; ?>" 
                            class="card-img-top img-fluid" 
                            alt="<?php echo htmlspecialchars($servico['nome']); ?>"
                            style="height: 200px; object-fit: cover;">
                        
                        <div class="card-body d-flex flex-column p-4">
                            <h4 class="card-title fw-bold text-dark mb-2">
                                <?php echo htmlspecialchars($servico['nome']); ?>
                            </h4>
                            
                            <p class="card-text text-secondary mb-4 flex-grow-1">
                                <?php echo htmlspecialchars($servico['descricao'] ?? 'Serviço automotivo completo com a qualidade WinCar.'); ?>
                            </p>

                            <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                                <span class="fs-4 fw-bold text-primary">
                                    R$ <?php echo number_format($servico['preco'], 2, ',', '.'); ?>
                                </span>
                                <a href="agendar.php" class="btn btn-outline-primary rounded-pill fw-bold">
                                    Agendar
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-4">
                <p class="text-muted">Nenhum serviço cadastrado no momento.</p>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php include 'includes/footer.php'; ?>