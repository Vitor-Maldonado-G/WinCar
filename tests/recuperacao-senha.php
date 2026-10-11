<?php
// As tabelas temporarias existem apenas nesta conexao, sem alterar contas reais.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../includes/recuperacao-senha.php';
$db = new PDO('mysql:host=localhost;dbname=wincar;charset=utf8mb4', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$db->exec('CREATE TEMPORARY TABLE cliente (id_cliente INT PRIMARY KEY, email VARCHAR(100), senha VARCHAR(255)) ENGINE=InnoDB');
$db->exec('CREATE TEMPORARY TABLE recuperacao_senha (id_cliente INT PRIMARY KEY, token_hash CHAR(64) UNIQUE, solicitado_em DATETIME, expira_em DATETIME, usado_em DATETIME NULL) ENGINE=InnoDB');
$db->prepare('INSERT INTO cliente VALUES (1, ?, ?)')->execute(['teste@example.com', password_hash('Antiga123', PASSWORD_DEFAULT)]);
function verificar($condicao, $nome) {
    if (!$condicao) { throw new RuntimeException($nome); }
    echo "OK: $nome\n";
}
verificar(criarRecuperacao($db, 'ausente@example.com') === null, 'Conta inexistente');
$primeiro = criarRecuperacao($db, 'teste@example.com');
verificar(tokenRecuperacaoValido($primeiro['token']), 'Token aleatorio valido');
verificar($db->query('SELECT token_hash FROM recuperacao_senha')->fetchColumn() === hash('sha256', $primeiro['token']), 'Somente hash armazenado');
verificar(criarRecuperacao($db, 'teste@example.com') === null, 'Intervalo de um minuto');
$db->exec('UPDATE recuperacao_senha SET solicitado_em = DATE_SUB(UTC_TIMESTAMP(), INTERVAL 2 MINUTE)');
$segundo = criarRecuperacao($db, 'teste@example.com');
verificar(!buscarRecuperacao($db, $primeiro['token']), 'Link anterior invalidado');
verificar(!redefinirSenha($db, $segundo['token'], 'fraca'), 'Senha fraca rejeitada');
verificar(redefinirSenha($db, $segundo['token'], ' Nova123 '), 'Senha atualizada');
$hash = $db->query('SELECT senha FROM cliente')->fetchColumn();
verificar(password_verify(' Nova123 ', $hash) && !password_verify('Antiga123', $hash), 'Nova senha valida e antiga rejeitada');
verificar(!redefinirSenha($db, $segundo['token'], 'Outra123'), 'Link de uso unico');
$db->exec('UPDATE recuperacao_senha SET usado_em = NULL, expira_em = DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 MINUTE)');
verificar(!buscarRecuperacao($db, $segundo['token']), 'Link expirado rejeitado');
verificar(!buscarRecuperacao($db, []), 'Token malformado rejeitado');
$_SESSION['csrf_recuperacao'] = 'teste';
verificar(csrfRecuperacaoValido('teste') && !csrfRecuperacaoValido('outro'), 'Protecao CSRF');
verificar(erroNovaSenha('Nova123', 'Outra123') !== null, 'Confirmacao de senha');
