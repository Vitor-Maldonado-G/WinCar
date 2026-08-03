<?php include 'includes/header.php'; ?>

<div class="row justify-content-center w-100 m-0">
    <div class="col-md-6 col-lg-5">
        <div class="card bg-primary text-white shadow border-0">
            <div class="card-body p-4">
                
                <h3 class="card-title text-center mb-4 fw-bold">Criar sua Conta</h3>
                
                <form action="processa_cadastro.php" method="POST">
                    
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

                    <div class="mb-4">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" placeholder="Crie uma senha forte" required>
                    </div>

                    <div class="d-grid">
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
function mascaraTelefone(input) {
    let v = input.value;
    
    // Remove tudo o que não for dígito
    v = v.replace(/\D/g, "");
    
    // Coloca parênteses em volta dos dois primeiros dígitos (DDD)
    v = v.replace(/^(\d{2})(\d)/g, "($1) $2");
    
    // Coloca hífen entre o quinto e o sexto dígitos (para celular de 9 dígitos)
    v = v.replace(/(\d{5})(\d)/, "$1-$2");
    
    // Limita o tamanho ao padrão (11) 99999-9999
    input.value = v.substring(0, 15);
}
</script>

<?php include 'includes/footer.php'; ?>