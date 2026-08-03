<?php include 'includes/header.php'; ?>

<div class="row justify-content-center w-100 m-0">
    <div class="col-md-6 col-lg-4">
        <div class="card bg-primary text-white shadow border-0">
            <div class="card-body p-4">
                
                <h3 class="card-title text-center mb-4 fw-bold">Acesse sua Conta</h3>
                
                <form action="processa_login.php" method="POST">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" required>
                    </div>

                    <div class="mb-4">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" placeholder="Digite sua senha" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-light text-primary fw-bold btn-lg">Entrar</button>
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
<?php include 'includes/footer.php'; ?>