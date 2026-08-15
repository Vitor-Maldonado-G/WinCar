<?php
session_start();

if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/conexao.php';

try {
    $stmtServicos = $conexao->query("SELECT * FROM servico");
    $servicos = $stmtServicos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $servicos = [];
}

include 'includes/header.php';
?>

<div class="container my-5" style="max-width: 600px;">

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

    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4 p-md-5">
            <h2 class="text-center fw-bold text-primary mb-4">Agendar Serviço</h2>

            <form action="processa_agendamento.php" method="POST" id="formAgendamento" onsubmit="return validarFormulario()" novalidate>
                
                <div class="mb-3">
                    <label for="modelo_busca" class="form-label fw-bold">Modelo do Veículo</label>
                    <input type="text" class="form-control" id="modelo_busca" list="listaModelos" placeholder="Digite marca ou modelo (ex: Nissan March)" autocomplete="off" maxlength="60" required>

                    <datalist id="listaModelos">
                        <option value="Chevrolet Onix">
                        <option value="Chevrolet Onix Plus">
                        <option value="Chevrolet Prisma">
                        <option value="Chevrolet Celta">
                        <option value="Chevrolet Cruze">
                        <option value="Chevrolet Tracker">
                        <option value="Chevrolet S10">
                        <option value="Fiat Uno">
                        <option value="Fiat Palio">
                        <option value="Fiat Argo">
                        <option value="Fiat Mobi">
                        <option value="Fiat Strada">
                        <option value="Fiat Toro">
                        <option value="Fiat Cronos">
                        <option value="Volkswagen Gol">
                        <option value="Volkswagen Voyage">
                        <option value="Volkswagen Polo">
                        <option value="Volkswagen Virtus">
                        <option value="Volkswagen T-Cross">
                        <option value="Volkswagen Saveiro">
                        <option value="Volkswagen Nivus">
                        <option value="Ford Ka">
                        <option value="Ford EcoSport">
                        <option value="Ford Ranger">
                        <option value="Hyundai HB20">
                        <option value="Hyundai Creta">
                        <option value="Hyundai Tucson">
                        <option value="Toyota Corolla">
                        <option value="Toyota Etios">
                        <option value="Toyota Hilux">
                        <option value="Toyota Yaris">
                        <option value="Toyota SW4">
                        <option value="Honda Civic">
                        <option value="Honda City">
                        <option value="Honda HR-V">
                        <option value="Honda Fit">
                        <option value="Renault Kwid">
                        <option value="Renault Sandero">
                        <option value="Renault Logan">
                        <option value="Renault Duster">
                        <option value="Nissan Kicks">
                        <option value="Nissan Versa">
                        <option value="Nissan March">
                        <option value="Jeep Renegade">
                        <option value="Jeep Compass">
                        <option value="Peugeot 208">
                        <option value="Citroën C3">
                    </datalist>

                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="naoEncontrei">
                        <label class="form-check-label" for="naoEncontrei">
                            Não encontrei meu veículo na lista
                        </label>
                    </div>

                    <div id="camposManuais" class="d-none mt-2">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="marca_manual" placeholder="Marca (ex: BYD)" maxlength="30">
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="modelo_manual" placeholder="Modelo (ex: Dolphin)" maxlength="30">
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="modelo" name="modelo">
                </div>

                <div class="mb-3">
                    <label for="placa" class="form-label fw-bold">Placa do Veículo</label>
                    <input type="text" class="form-control text-uppercase" id="placa" name="placa" placeholder="Ex: ABC1D23 ou ABC1234" maxlength="7" required>
                    <div class="invalid-feedback">Placa inválida. Use o formato ABC1D23 ou ABC1234.</div>
                    <div class="form-text">Apenas 7 caracteres (Ex: ABC1D23).</div>
                </div>

                <div class="mb-3">
                    <label for="id_servico" class="form-label fw-bold">Serviço Desejado</label>
                    <select class="form-select" id="id_servico" name="id_servico" required>
                        <option value="" selected disabled>Selecione um serviço...</option>
                        <?php foreach ($servicos as $servico): ?>
                            <option value="<?php echo $servico['id_servico']; ?>">
                                <?php echo $servico['nome'] ?? $servico['nome_servico']; ?> 
                                <?php if (isset($servico['preco'])): ?>
                                    - R$ <?php echo number_format($servico['preco'], 2, ',', '.'); ?>
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="data" class="form-label fw-bold">Data</label>
                        <input 
                                type="date" 
                                class="form-control" 
                                id="data" 
                                name="data" 
                                min="<?php echo date('Y-m-d'); ?>" 
                                max="<?php echo date('Y-m-d', strtotime('+60 days')); ?>" 
                                onkeydown="return false"
                                onclick="this.showPicker()" 
                                required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="hora" class="form-label fw-bold">Horário</label>
                        <select class="form-select" id="hora" name="hora" required>
                            <option value="" selected disabled>Escolha a hora...</option>
                            <option value="08:00">08:00</option>
                            <option value="09:00">09:00</option>
                            <option value="10:00">10:00</option>
                            <option value="11:00">11:00</option>
                            <option value="13:00">13:00</option>
                            <option value="14:00">14:00</option>
                            <option value="15:00">15:00</option>
                            <option value="16:00">16:00</option>
                            <option value="17:00">17:00</option>
                        </select>
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold">Confirmar Agendamento</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>

    document.getElementById('placa').addEventListener('input', function(e) {
        
        this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
    });

    
    // Campo de busca com autocomplete + fallback de marca/modelo manual
    const modeloBusca = document.getElementById('modelo_busca');
    const naoEncontrei = document.getElementById('naoEncontrei');
    const camposManuais = document.getElementById('camposManuais');
    const marcaManual = document.getElementById('marca_manual');
    const modeloManual = document.getElementById('modelo_manual');
    const modeloHidden = document.getElementById('modelo');

    function filtrarTexto(valor) {
        // Só permite letras, números, espaços e hífen — bloqueia símbolos aleatórios
        return valor.replace(/[^a-zA-ZÀ-ÿ0-9\s-]/g, '');
    }

    naoEncontrei.addEventListener('change', function() {
        if (this.checked) {
            modeloBusca.value = '';
            modeloBusca.setAttribute('disabled', 'disabled');
            modeloBusca.removeAttribute('required');
            camposManuais.classList.remove('d-none');
            marcaManual.setAttribute('required', 'required');
            modeloManual.setAttribute('required', 'required');
            modeloHidden.value = '';
            marcaManual.focus();
        } else {
            modeloBusca.removeAttribute('disabled');
            modeloBusca.setAttribute('required', 'required');
            camposManuais.classList.add('d-none');
            marcaManual.removeAttribute('required');
            modeloManual.removeAttribute('required');
            marcaManual.value = '';
            modeloManual.value = '';
            modeloHidden.value = modeloBusca.value.trim();
        }
    });

    modeloBusca.addEventListener('input', function() {
        this.value = filtrarTexto(this.value);
        modeloHidden.value = this.value.trim();
    });

    [marcaManual, modeloManual].forEach(function(campo) {
        campo.addEventListener('input', function() {
            this.value = filtrarTexto(this.value);
            modeloHidden.value = (marcaManual.value.trim() + ' ' + modeloManual.value.trim()).trim();
        });
    });

    // Consulta os horários já ocupados assim que a data é escolhida
    const campoData = document.getElementById('data');
    const campoHora = document.getElementById('hora');
    const horariosOriginais = Array.from(campoHora.options).map(function(opt) {
        return { value: opt.value, texto: opt.textContent };
    });

    campoData.addEventListener('change', function() {
        const dataEscolhida = this.value;
        if (!dataEscolhida) return;

        campoHora.disabled = true;
        campoHora.innerHTML = '<option value="" selected disabled>Carregando horários...</option>';

        fetch('horarios-disponiveis.php?data=' + encodeURIComponent(dataEscolhida))
            .then(function(resposta) {
                if (!resposta.ok) throw new Error('Falha ao consultar horários');
                return resposta.json();
            })
            .then(function(horariosOcupados) {
                campoHora.innerHTML = '';
                horariosOriginais.forEach(function(opt) {
                    if (opt.value === '' || !horariosOcupados.includes(opt.value)) {
                        const novaOpcao = document.createElement('option');
                        novaOpcao.value = opt.value;
                        novaOpcao.textContent = opt.value === '' ? opt.texto : opt.texto;
                        if (opt.value === '') {
                            novaOpcao.selected = true;
                            novaOpcao.disabled = true;
                        }
                        campoHora.appendChild(novaOpcao);
                    }
                });
                campoHora.disabled = false;
            })
            .catch(function() {
                // Se a consulta falhar, mantém todos os horários visíveis (não trava o usuário)
                campoHora.innerHTML = '';
                horariosOriginais.forEach(function(opt) {
                    const novaOpcao = document.createElement('option');
                    novaOpcao.value = opt.value;
                    novaOpcao.textContent = opt.texto;
                    if (opt.value === '') {
                        novaOpcao.selected = true;
                        novaOpcao.disabled = true;
                    }
                    campoHora.appendChild(novaOpcao);
                });
                campoHora.disabled = false;
            });
    });

    function validarFormulario() {
    const placaInput = document.getElementById('placa');
    const placa = placaInput.value.trim();
    const regexPlaca = /^[A-Z]{3}[0-9]{1}[A-Z0-9]{1}[0-9]{2}$/;

    if (!regexPlaca.test(placa)) {
        placaInput.classList.add('is-invalid');
        placaInput.focus();
        return false;
    }

    placaInput.classList.remove('is-invalid');

    if (!modeloHidden.value.trim()) {
        if (naoEncontrei.checked) {
            marcaManual.classList.add('is-invalid');
            marcaManual.focus();
        } else {
            modeloBusca.classList.add('is-invalid');
            modeloBusca.focus();
        }
        return false;
    }

    return true;
}

document.getElementById('placa').addEventListener('input', function() {
    this.classList.remove('is-invalid');
});
</script>

<?php include 'includes/footer.php'; ?>