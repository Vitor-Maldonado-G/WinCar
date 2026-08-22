<?php
require_once __DIR__ . '/index.php'; // guarda de sessão — só admin logado passa daqui
require_once '../config/conexao.php';

// Busca todos os agendamentos de todos os clientes, com nome do cliente e do serviço
try {
    $sql = "SELECT 
                a.id_agendamento AS id,
                c.nome  AS cliente,
                c.telefone AS telefone,
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
    <script>
        (function() {
            if (localStorage.getItem('wincar-tema') === 'dark') {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
            }
        })();
    </script>
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
                document.write('<link rel="stylesheet" href="../assets/css/bootstrap.min.css">');
            }
        })();
    </script>
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
      <button class="btn btn-outline-light btn-sm" id="toggleTema" type="button" title="Alternar tema">
          <i class="bi bi-moon-stars"></i>
      </button>
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
                            <th class="text-center pe-4">Contato</th>
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
                                        <?php if ($ag['status'] === 'Cancelado'): ?>
                                            <span class="text-muted small fst-italic" title="Agendamentos cancelados não podem mais ser alterados">
                                                <i class="bi bi-lock-fill me-1"></i>Cancelado
                                            </span>
                                        <?php else: ?>
                                            <form action="mudar-status.php" method="POST" class="d-inline">
                                                <input type="hidden" name="id_agendamento" value="<?php echo $ag['id']; ?>">
                                                <select name="novo_status" class="form-select form-select-sm" data-status-atual="<?php echo $ag['status']; ?>" style="min-width: 140px;" onchange="confirmarMudancaStatus(this)">
                                                    <option value="Pendente" <?php echo $ag['status'] == 'Pendente' ? 'selected' : ''; ?>>Pendente</option>
                                                    <option value="Confirmado" <?php echo $ag['status'] == 'Confirmado' ? 'selected' : ''; ?>>Confirmado</option>
                                                    <option value="Concluido" <?php echo $ag['status'] == 'Concluido' ? 'selected' : ''; ?>>Concluído</option>
                                                    <option value="Cancelado" <?php echo $ag['status'] == 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                                </select>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center pe-4">
                                        <?php
                                            // Monta o link do WhatsApp com uma mensagem que muda conforme o status
                                            $telefoneLimpo = preg_replace('/\D/', '', $ag['telefone']);
                                            $primeiroNomeCliente = explode(' ', trim($ag['cliente']))[0];
                                            $dataFormatadaWpp = date('d/m', strtotime($ag['data']));

                                            if ($ag['status'] === 'Concluido') {
                                                $mensagemWpp = "Olá, {$primeiroNomeCliente}! Aqui é da WinCar 🚗 Seu {$ag['modelo']} já está pronto para retirada!";
                                            } elseif ($ag['status'] === 'Confirmado') {
                                                $mensagemWpp = "Olá, {$primeiroNomeCliente}! Aqui é da WinCar 🚗 Confirmando seu agendamento de {$ag['servico']} no dia {$dataFormatadaWpp} às {$ag['hora']}.";
                                            } else {
                                                $mensagemWpp = "Olá, {$primeiroNomeCliente}! Aqui é da WinCar 🚗 Estamos entrando em contato sobre seu agendamento de {$ag['servico']}.";
                                            }

                                            $linkWpp = "https://wa.me/55{$telefoneLimpo}?text=" . urlencode($mensagemWpp);
                                        ?>
                                        <?php if (!empty($telefoneLimpo)): ?>
                                            <a href="<?php echo $linkWpp; ?>" target="_blank" rel="noopener" class="btn btn-success btn-sm rounded-pill" title="Enviar mensagem via WhatsApp">
                                                <i class="bi bi-whatsapp"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
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

<div class="modal fade" id="modalConfirmarStatus" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body p-4 text-center">
                <i class="bi bi-exclamation-triangle-fill text-warning mb-3" style="font-size: 2.5rem;"></i>
                <p class="fs-5 mb-0" id="modalConfirmarStatusTexto">Tem certeza?</p>
            </div>
            <div class="modal-footer border-0 p-4 pt-0 justify-content-center gap-2">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" id="btnCancelarStatus" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" id="btnConfirmarStatus">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<footer class="bg-dark text-white text-center py-3 mt-auto">
    <p class="mb-0">&copy; 2026 WinCar - Todos os direitos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const botaoTema = document.getElementById('toggleTema');
    const iconeTema = botaoTema.querySelector('i');

    function atualizarIconeTema() {
        if (document.documentElement.getAttribute('data-bs-theme') === 'dark') {
            iconeTema.classList.remove('bi-moon-stars');
            iconeTema.classList.add('bi-sun');
        } else {
            iconeTema.classList.remove('bi-sun');
            iconeTema.classList.add('bi-moon-stars');
        }
    }

    atualizarIconeTema();

    botaoTema.addEventListener('click', function() {
        const estaEscuro = document.documentElement.getAttribute('data-bs-theme') === 'dark';

        if (estaEscuro) {
            document.documentElement.removeAttribute('data-bs-theme');
            localStorage.setItem('wincar-tema', 'light');
        } else {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
            localStorage.setItem('wincar-tema', 'dark');
        }

        atualizarIconeTema();
    });

    // Modal de confirmação estilizado (substitui o confirm() nativo do navegador)
    const modalConfirmar = new bootstrap.Modal(document.getElementById('modalConfirmarStatus'));
    const modalTexto = document.getElementById('modalConfirmarStatusTexto');
    const btnConfirmar = document.getElementById('btnConfirmarStatus');
    let selectPendente = null;
    let valorOriginalPendente = null;

    function confirmarMudancaStatus(select) {
        const statusCritico = ['Cancelado', 'Concluido'];
        const valorOriginal = select.dataset.statusAtual;
        const novoValor = select.value;

        if (statusCritico.includes(novoValor) && novoValor !== valorOriginal) {
            selectPendente = select;
            valorOriginalPendente = valorOriginal;

            modalTexto.textContent = novoValor === 'Cancelado'
                ? 'Confirma o cancelamento deste agendamento?'
                : 'Confirma que este serviço foi concluído?';

            modalConfirmar.show();
            return;
        }

        select.form.submit();
    }

    btnConfirmar.addEventListener('click', function() {
        const formParaEnviar = selectPendente ? selectPendente.form : null;
        selectPendente = null; // evita que o evento de fechar o modal reverta o valor
        modalConfirmar.hide();
        if (formParaEnviar) {
            formParaEnviar.submit();
        }
    });

    document.getElementById('modalConfirmarStatus').addEventListener('hidden.bs.modal', function() {
        // Só reverte se o modal foi fechado SEM confirmar (Cancelar, X, ou clique fora)
        if (selectPendente) {
            selectPendente.value = valorOriginalPendente;
        }
        selectPendente = null;
        valorOriginalPendente = null;
    });

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