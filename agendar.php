<?php
session_start();

if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['id_cliente'])) {
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
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4 p-md-5">
            <h2 class="text-center fw-bold text-primary mb-4">Agendar Serviço</h2>

            <form action="processa_agendamento.php" method="POST" id="formAgendamento" onsubmit="return validarFormulario()">
                
                <div class="mb-3">
                    <label for="modelo" class="form-label fw-bold">Modelo do Veículo</label>
                    <input type="text" class="form-control" id="modelo" name="modelo" placeholder="Ex: Civic 2.0, Gol 1.0..." required>
                </div>

                <div class="mb-3">
                    <label for="placa" class="form-label fw-bold">Placa do Veículo</label>
                    <input type="text" class="form-control text-uppercase" id="placa" name="placa" placeholder="Ex: ABC1D23 ou ABC1234" maxlength="7" required>
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

                <input 
                        type="date" 
                        class="form-control" 
                        id="data" 
                        name="data" 
                        min="<?php echo date('Y-m-d'); ?>" 
                        max="<?php echo date('Y-m-d', strtotime('+60 days')); ?>" 
                        onkeydown="return false" 
                        required>

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

    
    function validarFormulario() {
        const placaInput = document.getElementById('placa');
        const placa = placaInput.value.trim();
        const regexPlaca = /^[A-Z]{3}[0-9]{1}[A-Z0-9]{1}[0-9]{2}$/;

        if (!regexPlaca.test(placa)) {
            alert('Placa inválida! Digite no padrão correto de 7 caracteres (Ex: ABC1D23 ou ABC1234).');
            placaInput.focus();
            return false;
        }

        return true;
    }
</script>

<?php include 'includes/footer.php'; ?>