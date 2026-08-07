<?php
require_once __DIR__ . '/index.php'; // guarda de sessão — só admin logado passa daqui
require_once '../config/conexao.php';

// Busca todos os agendamentos de todos os clientes, com nome do cliente e do serviço
try {
    $sql = "SELECT 
                a.id_agendamento AS id,
                c.nome  AS cliente,
                a.modelo,
                a.placa,
                s.nome  AS servico,
                a.data,
                a.hora,
                a.status
            FROM agendamento a
            INNER JOIN cliente c ON a.id_cliente = c.id_cliente
            INNER JOIN servico s ON a.id_servico = s.id_servico
            ORDER BY a.data DESC, a.hora DESC";

    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $agendamentos = [];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WinCar - Painel Admin</title>
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/estilo.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid px-4 d-flex justify-content-between align-items-center">

    <span class="navbar-brand fw-bold d-flex align-items-center">
      <img src="../assets/img/logowincar.png" alt="WinCar Logo" height="40" class="me-2">
      WinCar <span class="badge bg-primary ms-2 fs-6">Admin</span>
    </span>

    <div class="d-flex align-items-center gap-3">
      <span class="text-white fw-semibold">Olá, Admin!</span>
      <a href="../logout.php" class="btn btn-outline-light btn-sm">Sair</a>
    </div>

  </div>
</nav>

<div class="container my-5 flex-grow-1">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-1">Painel Administrativo</h2>
            <p class="text-muted mb-0">Acompanhe e gerencie todos os agendamentos da WinCar.</p>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-4" id="filtrosStatus">
        <button class="btn btn-primary btn-sm rounded-pill px-3 filtro-btn active" data-filtro="Todos">Todos</button>
        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 filtro-btn" data-filtro="Pendente">Pendente</button>
        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 filtro-btn" data-filtro="Confirmado">Confirmado</button>
        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 filtro-btn" data-filtro="Concluido">Concluído</button>
        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 filtro-btn" data-filtro="Cancelado">Cancelado</button>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0" id="tabelaAgendamentos">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Cliente</th>
                            <th>Veículo</th>
                            <th>Placa</th>
                            <th>Serviço</th>
                            <th>Data</th>
                            <th>Horário</th>
                            <th>Status</th>
                            <th class="text-center pe-4">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($agendamentos)): ?>
                            <?php foreach ($agendamentos as $ag): ?>
                                <?php
                                    $badgeClass = 'bg-warning text-dark';
                                    if ($ag['status'] == 'Confirmado') {
                                        $badgeClass = 'bg-info text-white';
                                    } elseif ($ag['status'] == 'Concluido') {
                                        $badgeClass = 'bg-success text-white';
                                    } elseif ($ag['status'] == 'Cancelado') {
                                        $badgeClass = 'bg-danger text-white';
                                    }

                                    $dataFormatada = date('d/m/Y', strtotime($ag['data']));
                                    $horaFormatada = date('H:i', strtotime($ag['hora']));
                                ?>
                                <tr data-status="<?php echo htmlspecialchars($ag['status']); ?>">
                                    <td class="ps-4 fw-bold text-dark"><?php echo htmlspecialchars($ag['cliente']); ?></td>
                                    <td><?php echo htmlspecialchars($ag['modelo']); ?></td>
                                    <td>
                                        <span class="badge bg-secondary text-uppercase fs-6">
                                            <?php echo htmlspecialchars($ag['placa']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($ag['servico']); ?></td>
                                    <td><?php echo $dataFormatada; ?></td>
                                    <td><?php echo $horaFormatada; ?></td>
                                    <td>
                                        <span class="badge <?php echo $badgeClass; ?> px-3 py-2 rounded-pill fs-6">
                                            <?php echo $ag['status'] == 'Concluido' ? 'Concluído' : htmlspecialchars($ag['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center pe-4">
                                        <form action="mudar-status.php" method="POST" class="d-inline">
                                            <input type="hidden" name="id_agendamento" value="<?php echo $ag['id']; ?>">
                                            <select name="novo_status" class="form-select form-select-sm" style="min-width: 140px;" onchange="this.form.submit()">
                                                <option value="Pendente" <?php echo $ag['status'] == 'Pendente' ? 'selected' : ''; ?>>Pendente</option>
                                                <option value="Confirmado" <?php echo $ag['status'] == 'Confirmado' ? 'selected' : ''; ?>>Confirmado</option>
                                                <option value="Concluido" <?php echo $ag['status'] == 'Concluido' ? 'selected' : ''; ?>>Concluído</option>
                                                <option value="Cancelado" <?php echo $ag['status'] == 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    Nenhum agendamento encontrado.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<footer class="bg-dark text-white text-center py-3 mt-auto">
    <p class="mb-0">&copy; 2026 WinCar - Todos os direitos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.filtro-btn').forEach(function (botao) {
        botao.addEventListener('click', function () {
            document.querySelectorAll('.filtro-btn').forEach(function (b) {
                b.classList.remove('btn-primary', 'active');
                b.classList.add('btn-outline-secondary');
            });
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-primary', 'active');

            const filtro = this.dataset.filtro;
            document.querySelectorAll('#tabelaAgendamentos tbody tr').forEach(function (linha) {
                if (filtro === 'Todos' || linha.dataset.status === filtro) {
                    linha.style.display = '';
                } else {
                    linha.style.display = 'none';
                }
            });
        });
    });
</script>

</body>
</html>