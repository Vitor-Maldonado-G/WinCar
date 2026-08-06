<?php
session_start();

// Proteção da página: só entra se estiver logado
if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/conexao.php';

$id_cliente = $_SESSION['usuario_id'] ?? $_SESSION['usuario_id'];

// 1. Busca os dados cadastrais do cliente
try {
    $sqlCliente = "SELECT nome, email, telefone FROM cliente WHERE id_cliente = :id_cliente";
    $stmtCliente = $conexao->prepare($sqlCliente);
    $stmtCliente->bindParam(':id_cliente', $id_cliente);
    $stmtCliente->execute();
    $cliente = $stmtCliente->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $cliente = null;
}

// 2. Busca os agendamentos do cliente logado + o nome do serviço
try {
    $sqlAgendamentos = "SELECT a.*, s.nome AS nome_servico, s.preco 
                        FROM agendamento a
                        INNER JOIN servico s ON a.id_servico = s.id_servico
                        WHERE a.id_cliente = :id_cliente
                        ORDER BY a.data DESC, a.hora DESC";

    $stmtAgendamentos = $conexao->prepare($sqlAgendamentos);
    $stmtAgendamentos->bindParam(':id_cliente', $id_cliente);
    $stmtAgendamentos->execute();
    $meus_agendamentos = $stmtAgendamentos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $meus_agendamentos = [];
}

include 'includes/header.php';
?>

<div class="container my-5">
    
    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-<?php echo $_SESSION['tipo_mensagem']; ?> alert-dismissible fade show rounded-4 mb-4" role="alert">
            <?php 
                echo $_SESSION['mensagem']; 
                unset($_SESSION['mensagem']);
                unset($_SESSION['tipo_mensagem']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h1 class="fw-bold text-primary mb-1">Esse é o seu painel</h1>
            <p class="text-secondary fs-5 mb-0">Aqui você pode ver seus agendamentos e realizar outros</p>
        </div>
        <a href="agendar.php" class="btn btn-primary btn-lg rounded-pill shadow-sm fw-bold">
            + Novo Agendamento
        </a>
    </div>

    <?php if ($cliente): ?>
        <div class="card shadow-sm border-0 rounded-4 mb-4 bg-light">
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-3">Minhas Informações</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <span class="text-muted d-block small">Nome:</span>
                        <strong class="fs-6 text-dark"><?php echo htmlspecialchars($cliente['nome'] ?? 'Não informado'); ?></strong>
                    </div>
                    <div class="col-md-4">
                        <span class="text-muted d-block small">E-mail:</span>
                        <strong class="fs-6 text-dark"><?php echo htmlspecialchars($cliente['email'] ?? 'Não informado'); ?></strong>
                    </div>
                    <div class="col-md-4">
                        <span class="text-muted d-block small">Telefone:</span>
                        <strong class="fs-6 text-dark"><?php echo htmlspecialchars($cliente['telefone'] ?? 'Não informado'); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4">
            <h5 class="fw-bold text-primary mb-3">Seus Agendamentos</h5>
            
            <?php if (empty($meus_agendamentos)): ?>
                <div class="text-center py-5">
                    <h5 class="text-muted mb-3">Você ainda não possui nenhum agendamento.</h5>
                    <a href="agendar.php" class="btn btn-outline-primary rounded-pill fw-bold">Agendar meu primeiro serviço</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Veículo</th>
                                <th>Placa</th>
                                <th>Serviço</th>
                                <th>Data</th>
                                <th>Horário</th>
                                <th>Status</th>
                                <th class="text-center">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($meus_agendamentos as $agendamento): ?>
                                <tr>
                                    <td class="fw-bold text-dark">
                                        <?php echo htmlspecialchars($agendamento['modelo']); ?>
                                    </td>

                                    <td>
                                        <span class="badge bg-secondary text-uppercase fs-6">
                                            <?php echo htmlspecialchars($agendamento['placa']); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($agendamento['nome_servico']); ?>
                                        <br>
                                        <small class="text-muted">
                                            R$ <?php echo number_format($agendamento['preco'], 2, ',', '.'); ?>
                                        </small>
                                    </td>

                                    <td>
                                        <?php echo date('d/m/Y', strtotime($agendamento['data'])); ?>
                                    </td>

                                    <td>
                                        <?php echo date('H:i', strtotime($agendamento['hora'])); ?>
                                    </td>

                                    <td>
                                        <?php
                                            $status = $agendamento['status'];
                                            $badgeClass = 'bg-warning text-dark';

                                            if ($status == 'Confirmado') {
                                                $badgeClass = 'bg-info text-white';
                                            } elseif ($status == 'Concluido' || $status == 'Concluído') {
                                                $badgeClass = 'bg-success text-white';
                                            } elseif ($status == 'Cancelado') {
                                                $badgeClass = 'bg-danger text-white';
                                            }
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?> px-3 py-2 rounded-pill fs-6">
                                            <?php echo htmlspecialchars($status); ?>
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <?php if ($agendamento['status'] != 'Cancelado' && $agendamento['status'] != 'Concluido' && $agendamento['status'] != 'Concluído'): ?>
                                            <a href="cancelar.php?id=<?php echo $agendamento['id_agendamento']; ?>" 
                                               class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold"
                                               onclick="return confirm('Tem certeza que deseja cancelar este agendamento?');">
                                                Cancelar
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>