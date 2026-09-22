<?php
session_start();

// Proteção da página: só entra se estiver logado
if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/conexao.php';

if (empty($_SESSION['csrf_perfil'])) {
    $_SESSION['csrf_perfil'] = bin2hex(random_bytes(32));
}

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
                <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                    <h5 class="fw-bold text-dark mb-0">Minhas Informações</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditarPerfil">
                        <i class="bi bi-pencil me-1" aria-hidden="true"></i>Editar
                    </button>
                </div>
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
                                            <button type="button"
                                               class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold"
                                               data-bs-toggle="modal"
                                               data-bs-target="#modalCancelarAgendamento"
                                               data-id-agendamento="<?php echo $agendamento['id_agendamento']; ?>">
                                                Cancelar
                                            </button>
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

<?php if ($cliente): ?>
<div class="modal fade" id="modalEditarPerfil" tabindex="-1" aria-labelledby="modalEditarPerfilLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="atualizar-perfil.php" method="POST">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="modalEditarPerfilLabel">Editar minhas informações</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_perfil'], ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="mb-3">
                        <label for="nomePerfil" class="form-label fw-bold">Nome</label>
                        <input type="text" class="form-control" id="nomePerfil" name="nome" value="<?php echo htmlspecialchars($cliente['nome'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="100" required>
                    </div>
                    <div>
                        <label for="telefonePerfil" class="form-label fw-bold">Telefone / WhatsApp</label>
                        <input type="tel" class="form-control" id="telefonePerfil" name="telefone" value="<?php echo htmlspecialchars($cliente['telefone'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="(11) 99999-9999" maxlength="15" pattern="\([0-9]{2}\) [0-9]{5}-[0-9]{4}" oninput="mascaraTelefone(this)" required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal de confirmação de cancelamento -->
<div class="modal fade" id="modalCancelarAgendamento" tabindex="-1" aria-labelledby="modalCancelarAgendamentoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="modalCancelarAgendamentoLabel">Cancelar agendamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja cancelar este agendamento? Essa ação não pode ser desfeita.
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Voltar</button>
                <a href="#" id="btnConfirmarCancelamento" class="btn btn-danger rounded-pill px-4 fw-bold">Sim, cancelar</a>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/telefone.js"></script>
<script>
    // Ao abrir o modal, monta o link de cancelamento com o id do agendamento clicado
    const modalCancelar = document.getElementById('modalCancelarAgendamento');
    modalCancelar.addEventListener('show.bs.modal', function(evento) {
        const botaoClicado = evento.relatedTarget;
        const idAgendamento = botaoClicado.getAttribute('data-id-agendamento');
        const btnConfirmar = document.getElementById('btnConfirmarCancelamento');
        btnConfirmar.href = 'cancelar.php?id=' + encodeURIComponent(idAgendamento);
    });
</script>

<script>
    // Atualiza a página automaticamente a cada 30 segundos,
    // pra refletir mudanças de status feitas pelo admin
    setInterval(function() {
        if (!document.querySelector('.modal.show')) {
            location.reload();
        }
    }, 30000);
</script>

<?php include 'includes/footer.php'; ?>
