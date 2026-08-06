<?php 
session_start();
include 'includes/header.php'; 
?>

<?php if (isset($_SESSION['mensagem'])): ?>
    <div class="row justify-content-center w-100 m-0 my-3">
        <div class="col-md-6 col-lg-5">
            <div class="alert alert-<?php echo $_SESSION['tipo_mensagem']; ?> alert-dismissible fade show shadow-sm rounded-3" role="alert">
                <?php 
                    echo $_SESSION['mensagem']; 
                    unset($_SESSION['mensagem']);
                    unset($_SESSION['tipo_mensagem']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row justify-content-center w-100 m-0">
    <div class="col-md-6 col-lg-5">
        <div class="card bg-primary text-white shadow border-0">
            <div class="card-body p-4">
                
                <h3 class="card-title text-center mb-4 fw-bold">Criar sua Conta</h3>
                
                <form action="processa_cadastro.php" method="POST" id="formCadastro" novalidate>
                    
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="nome" name="nome" placeholder="Digite seu nome" required>
                    </div>

                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone / WhatsApp</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="telefone" 
                            name="telefone" 
                            placeholder="(11) 99999-9999" 
                            maxlength="15"
                            oninput="mascaraTelefone(this)"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" required>
                    </div>

                    <div class="mb-3">
                        <label for="senha" class="form-label fw-bold text-white">Senha</label>
                        <div class="input-group has-validation">
                            <input type="password" 
                                class="form-control" 
                                id="senha" 
                                name="senha" 
                                placeholder="Crie uma senha forte" 
                                minlength="6"
                                required>
                            <button class="btn btn-outline-light" type="button" onclick="alternarSenha('senha', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                            <div class="invalid-feedback">
                                A senha precisa de no mínimo 6 caracteres, 1 letra maiúscula e 1 número.
                            </div>
                        </div>
                        <div class="form-text text-light opacity-75">
                            A senha deve ter no mínimo 6 caracteres, contendo pelo menos 1 letra maiúscula e 1 número.
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-light text-primary fw-bold btn-lg">Cadastrar</button>
                    </div>

                </form>

                <hr class="my-4 border-light">

                <p class="text-center mb-0">
                    Já tem uma conta? <a href="login.php" class="text-white fw-bold text-decoration-underline">Faça login</a>
                </p>

            </div>
        </div>
    </div>
</div>

<script>
// Máscara para o Telefone
function mascaraTelefone(input) {
    let v = input.value;
    v = v.replace(/\D/g, ""); // Remove não dígitos
    v = v.replace(/^(\d{2})(\d)/g, "($1) $2"); // DDD
    v = v.replace(/(\d{5})(\d)/, "$1-$2"); // Hífen no celular
    input.value = v.substring(0, 15);
}

// Validação de Senha Forte
function validarSenha(senha) {
    // Regex: mín 6 chars, pelo menos 1 maiúscula (?=.*[A-Z]), pelo menos 1 número (?=.*\d)
    const regexSenha = /^(?=.*[A-Z])(?=.*\d).{6,}$/;
    return regexSenha.test(senha);
}

// Intercepta o envio do formulário
document.getElementById('formCadastro').addEventListener('submit', function(e) {
    const senhaInput = document.getElementById('senha');
    const senha = senhaInput.value;

    if (!validarSenha(senha)) {
        e.preventDefault();
        senhaInput.classList.add('is-invalid');
        senhaInput.focus();
    } else {
        senhaInput.classList.remove('is-invalid');
    }
});

document.getElementById('senha').addEventListener('input', function() {
    this.classList.remove('is-invalid');
});
</script>

<?php include 'includes/footer.php'; ?>