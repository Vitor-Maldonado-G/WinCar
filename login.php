<?php include 'includes/header.php'; ?>

<div class="row justify-content-center w-100 m-0">
    <div class="col-md-6 col-lg-4">

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

        <div class="card bg-primary text-white shadow border-0">
            <div class="card-body p-4">
                
                <h3 class="card-title text-center mb-4 fw-bold">Acesse sua Conta</h3>
                
                <form action="processa_login.php" method="POST" id="formLogin">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" required>
                    </div>

                    <div class="mb-4">
                        <label for="senha" class="form-label">Senha</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="senha" name="senha" placeholder="Digite sua senha" required>
                            <button class="btn btn-outline-light" type="button" onclick="alternarSenha('senha', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-light text-primary fw-bold btn-lg" id="btnEntrar">Entrar</button>
                    </div>

                </form>

                <hr class="my-4 border-light">

                <p class="text-center mb-0">
                    Ainda não tem conta? <a href="cadastro.php" class="text-white fw-bold text-decoration-underline">Cadastre-se aqui</a>
                </p>

            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('formLogin').addEventListener('submit', function() {
        const botao = document.getElementById('btnEntrar');
        botao.disabled = true;
        botao.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Entrando...';
    });
</script>

<?php include 'includes/footer.php'; ?>