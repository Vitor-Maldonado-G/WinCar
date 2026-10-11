<?php
session_start();
header('Cache-Control: no-store');
header('Referrer-Policy: no-referrer');
require_once 'includes/recuperacao-senha.php';

if (empty($_SESSION['csrf_recuperacao'])) {
    $_SESSION['csrf_recuperacao'] = bin2hex(random_bytes(32));
}

// Retira o token da URL antes de carregar fontes, imagens e demais recursos da pagina.
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    unset($_SESSION['token_redefinicao']);
    if (tokenRecuperacaoValido($_GET['token'])) {
        $_SESSION['token_redefinicao'] = $_GET['token'];
    }
    header('Location: redefinir-senha.php');
    exit();
}

$token = $_SESSION['token_redefinicao'] ?? '';
$linkValido = false;
$erro = null;
try {
    if (tokenRecuperacaoValido($token)) {
        require_once 'config/conexao.php';
        $linkValido = (bool) buscarRecuperacao($conexao, $token);
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $linkValido) {
        if (!csrfRecuperacaoValido($_POST['csrf_token'] ?? null)) {
            $erro = 'A página expirou. Recarregue e tente novamente.';
        } else {
            $erro = erroNovaSenha($_POST['senha'] ?? null, $_POST['confirmar_senha'] ?? null);
            if ($erro === null) {
                if (redefinirSenha($conexao, $token, $_POST['senha'])) {
                    unset($_SESSION['token_redefinicao'], $_SESSION['csrf_recuperacao'],
                        $_SESSION['usuario_id'], $_SESSION['usuario_nome'], $_SESSION['tipo_usuario']);
                    session_regenerate_id(true);
                    redirecionarRecuperacao('Senha atualizada! Entre com sua nova senha.', 'success', 'login.php');
                }
                $linkValido = false;
            }
        }
    }
} catch (Throwable $e) {
    error_log('WinCar: falha ao redefinir senha. Verifique o banco de dados.');
    $erro = 'Não foi possível redefinir sua senha. Tente novamente mais tarde.';
}

include 'includes/header.php';
?>
<div class="row justify-content-center w-100 m-0">
    <div class="col-md-6 col-lg-4">
        <?php if ($erro): ?>
            <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>
        <div class="card bg-primary text-white shadow border-0">
            <div class="card-body p-4">
                <?php if ($linkValido): ?>
                    <h1 class="h3 text-center fw-bold mb-3">Definir nova senha</h1>
                    <p>Use no mínimo 6 caracteres, uma letra maiúscula e um número.</p>
                    <form action="redefinir-senha.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_recuperacao']); ?>">
                        <div class="mb-3">
                            <label for="senha" class="form-label">Nova senha</label>
                            <input type="password" class="form-control" id="senha" name="senha" minlength="6" maxlength="72" autocomplete="new-password" required>
                        </div>
                        <div class="mb-4">
                            <label for="confirmar_senha" class="form-label">Confirmar nova senha</label>
                            <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" minlength="6" maxlength="72" autocomplete="new-password" required>
                        </div>
                        <div class="d-grid"><button type="submit" class="btn btn-light text-primary fw-bold">Salvar nova senha</button></div>
                    </form>
                <?php else: ?>
                    <h1 class="h3 text-center fw-bold mb-3">Link indisponível</h1>
                    <p>Este link é inválido, expirou ou já foi utilizado.</p>
                    <a href="recuperar-senha.php" class="btn btn-light text-primary fw-bold">Solicitar novo link</a>
                <?php endif; ?>
                <p class="text-center mt-4 mb-0"><a href="login.php" class="text-white text-decoration-underline">Voltar para o login</a></p>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
