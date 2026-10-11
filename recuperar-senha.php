<?php
session_start();
header('Cache-Control: no-store');
require_once 'includes/recuperacao-senha.php';
require_once 'includes/enviar-email.php';

if (empty($_SESSION['csrf_recuperacao'])) {
    $_SESSION['csrf_recuperacao'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfRecuperacaoValido($_POST['csrf_token'] ?? null)) {
        redirecionarRecuperacao('A página expirou. Tente novamente.', 'warning', 'recuperar-senha.php');
    }
    $email = $_POST['email'] ?? '';
    if (!is_string($email) || strlen($email) > 100 || !filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
        redirecionarRecuperacao('Informe um e-mail válido.', 'danger', 'recuperar-senha.php');
    }
    $mensagem = 'Se houver uma conta com esse e-mail, você receberá um link para redefinir sua senha. Confira também a pasta de spam e aguarde um minuto antes de solicitar outro.';
    if (time() - ($_SESSION['ultima_recuperacao'] ?? 0) < 60) {
        redirecionarRecuperacao($mensagem, 'success', 'recuperar-senha.php');
    }
    $_SESSION['ultima_recuperacao'] = time();

    try {
        $config = configuracaoEmail();
    } catch (Throwable $e) {
        error_log('WinCar: configure o e-mail de recuperacao em config/email.local.php.');
        redirecionarRecuperacao('A recuperação de senha está temporariamente indisponível. Tente novamente mais tarde.', 'warning', 'recuperar-senha.php');
    }

    require_once 'config/conexao.php';
    try {
        $recuperacao = criarRecuperacao($conexao, trim($email));
        if ($recuperacao) {
            try {
                enviarEmailRecuperacao($config, $recuperacao['email'], $recuperacao['token']);
            } catch (Throwable $e) {
                // Invalida o link que falhou sem remover o intervalo de seguranca entre envios.
                $stmt = $conexao->prepare('UPDATE recuperacao_senha SET usado_em = UTC_TIMESTAMP() WHERE token_hash = :hash');
                $stmt->execute([':hash' => hash('sha256', $recuperacao['token'])]);
                error_log('WinCar: falha no envio SMTP da recuperacao. Verifique a senha de app, a rede e o certificado TLS.');
            }
        }
    } catch (Throwable $e) {
        error_log('WinCar: falha ao processar recuperacao de senha. Verifique a tabela recuperacao_senha e o banco.');
    }
    // A mesma resposta evita revelar quais enderecos possuem conta.
    redirecionarRecuperacao($mensagem, 'success', 'recuperar-senha.php');
}

include 'includes/header.php';
?>
<div class="row justify-content-center w-100 m-0">
    <div class="col-md-6 col-lg-4">
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="alert alert-<?php echo htmlspecialchars($_SESSION['tipo_mensagem']); ?>" role="alert">
                <?php echo htmlspecialchars($_SESSION['mensagem']); unset($_SESSION['mensagem'], $_SESSION['tipo_mensagem']); ?>
            </div>
        <?php endif; ?>
        <div class="card bg-primary text-white shadow border-0">
            <div class="card-body p-4">
                <h1 class="h3 text-center fw-bold mb-3">Recuperar senha</h1>
                <p>Informe o e-mail da sua conta para receber um link de recuperação.</p>
                <form action="recuperar-senha.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_recuperacao']); ?>">
                    <div class="mb-4">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" maxlength="100" autocomplete="email" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-light text-primary fw-bold">Enviar link de recuperação</button>
                    </div>
                </form>
                <p class="text-center mt-4 mb-0"><a href="login.php" class="text-white text-decoration-underline">Voltar para o login</a></p>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
