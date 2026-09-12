<?php
session_start();

// Conexão com o banco de dados
require_once 'config/conexao.php';

// Busca a lista de serviços ativos no banco, agrupados por categoria.
try {
    $stmt = $conexao->query(
        "SELECT * FROM servico 
         ORDER BY FIELD(categoria, 'Simples', 'Intermediário', 'Premium'), id_servico"
    );
    $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $servicos = [];
}

// Agrupa os serviços por categoria pra exibir em seções
$servicosPorCategoria = [];
foreach ($servicos as $servico) {
    $servicosPorCategoria[$servico['categoria']][] = $servico;
}

// Cor de destaque de cada categoria
$corCategoria = [
    'Simples'        => 'success',
    'Intermediário'  => 'warning',
    'Premium'        => 'danger',
];

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

    <?php if (!empty($servicosPorCategoria)): ?>
        <?php foreach ($servicosPorCategoria as $categoria => $itensCategoria): ?>

            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge bg-<?php echo $corCategoria[$categoria] ?? 'secondary'; ?> rounded-pill" style="width: 14px; height: 14px; padding: 0;"></span>
                <h4 class="fw-bold mb-0"><?php echo htmlspecialchars($categoria); ?></h4>
            </div>

            <div class="row g-4 mb-5">
                <?php foreach ($itensCategoria as $servico): ?>
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

                                <?php if (!empty($servico['duracao'])): ?>
                                    <p class="text-secondary small mb-3">
                                        <i class="bi bi-clock me-1" aria-hidden="true"></i>
                                        Duração estimada: <?php echo (int) $servico['duracao']; ?> min
                                    </p>
                                <?php endif; ?>

                                <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                                    <span class="fs-4 fw-bold text-primary">
                                        R$ <?php echo number_format($servico['preco'], 2, ',', '.'); ?>
                                    </span>
                                    <?php if (($_SESSION['tipo_usuario'] ?? '') !== 'admin'): ?>
                                    <a href="agendar.php" class="btn btn-outline-primary rounded-pill fw-bold">
                                        Agendar
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center py-4">
            <p class="text-muted">Nenhum serviço cadastrado no momento.</p>
        </div>
    <?php endif; ?>

</section>

<section class="container mb-5" aria-labelledby="titulo-pagamento">
    <div class="text-center mb-4">
        <h2 id="titulo-pagamento" class="fw-bold text-primary">Formas de Pagamento</h2>
        <p class="text-muted">O pagamento é realizado exclusivamente no local, na retirada do veículo.</p>
    </div>
    <div class="row g-4">
        <div class="col-12 col-md-4">
            <div class="card card-pagamento h-100 text-center p-4">
                <i class="bi bi-cash-stack fs-1 text-primary mb-3" aria-hidden="true"></i>
                <h3 class="h5 fw-bold mb-0">Dinheiro</h3>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card card-pagamento h-100 text-center p-4">
                <i class="bi bi-credit-card fs-1 text-primary mb-3" aria-hidden="true"></i>
                <h3 class="h5 fw-bold mb-0">Cartão</h3>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card card-pagamento h-100 text-center p-4">
                <i class="bi bi-qr-code fs-1 text-primary mb-3" aria-hidden="true"></i>
                <h3 class="h5 fw-bold mb-0">Pix</h3>
            </div>
        </div>
    </div>
</section>

<section class="container mb-5 pt-4 border-top" aria-labelledby="titulo-contato">
    <h2 id="titulo-contato" class="fw-bold text-primary text-center mb-4">Contato &amp; Horários</h2>
    <div class="row g-4">
        <div class="col-12 col-md-6">
            <h3 class="h5 fw-bold"><i class="bi bi-chat-dots text-primary me-2" aria-hidden="true"></i>Contato</h3>
            <p class="mb-2">Telefone: (11) 0000-0000</p>
            <p class="mb-2">E-mail: contato@wincar.example</p>
            <p class="text-muted mb-0">Contatos fictícios para demonstração.</p>
        </div>
        <div class="col-12 col-md-6">
            <h3 class="h5 fw-bold"><i class="bi bi-clock text-primary me-2" aria-hidden="true"></i>Horários de agendamento</h3>
            <p class="mb-2">Segunda a sábado: das 08h às 11h e das 13h às 17h.</p>
            <p class="mb-2">Domingo: fechado.</p>
            <p class="text-muted mb-0">Consulte os horários disponíveis ao agendar.</p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
