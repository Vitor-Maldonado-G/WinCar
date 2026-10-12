<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../includes/exclusao-conta.php';

// As tabelas temporarias ocultam as reais apenas nesta conexao e somem ao encerrar.
$db = new PDO('mysql:host=localhost;dbname=wincar;charset=utf8mb4', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$db->exec('CREATE TEMPORARY TABLE cliente (id_cliente INT PRIMARY KEY, senha VARCHAR(255), tipo_usuario VARCHAR(20)) ENGINE=InnoDB');
$db->exec('CREATE TEMPORARY TABLE agendamento (id_cliente INT, data DATE, hora TIME, status VARCHAR(20)) ENGINE=InnoDB');
$db->exec('CREATE TEMPORARY TABLE veiculo (id_cliente INT) ENGINE=InnoDB');
$db->exec('CREATE TEMPORARY TABLE recuperacao_senha (id_cliente INT) ENGINE=InnoDB');
$senha = password_hash('Teste123', PASSWORD_DEFAULT);
foreach ([1 => 'cliente', 2 => 'cliente', 3 => 'admin'] as $id => $tipo) {
    $db->prepare('INSERT INTO cliente VALUES (?, ?, ?)')->execute([$id, $senha, $tipo]);
    $db->prepare("INSERT INTO agendamento VALUES (?, '2099-01-05', ?, 'Pendente')")->execute([$id, sprintf('%02d:00:00', $id + 8)]);
    $db->prepare('INSERT INTO veiculo VALUES (?)')->execute([$id]);
    $db->prepare('INSERT INTO recuperacao_senha VALUES (?)')->execute([$id]);
}
function conferir($condicao, $nome) {
    if (!$condicao) { throw new RuntimeException($nome); }
    echo "OK: $nome\n";
}
conferir(!excluirContaCliente($db, 1, 'errada'), 'Senha incorreta rejeitada');
conferir(!excluirContaCliente($db, 1, []), 'Senha malformada rejeitada');
conferir(!excluirContaCliente($db, 3, 'Teste123'), 'Administrador protegido');
conferir(!excluirContaCliente($db, 99, 'Teste123'), 'Conta inexistente rejeitada');
conferir((int)$db->query('SELECT COUNT(*) FROM agendamento')->fetchColumn() === 3, 'Falhas nao apagam agendamentos');
conferir(excluirContaCliente($db, 1, 'Teste123'), 'Exclusao confirmada');
foreach (['cliente', 'agendamento', 'veiculo', 'recuperacao_senha'] as $tabela) {
    conferir((int)$db->query("SELECT COUNT(*) FROM $tabela WHERE id_cliente = 1")->fetchColumn() === 0, "Dados removidos: $tabela");
    conferir((int)$db->query("SELECT COUNT(*) FROM $tabela WHERE id_cliente = 2")->fetchColumn() === 1, "Outra conta preservada: $tabela");
}
conferir((int)$db->query("SELECT COUNT(*) FROM agendamento WHERE data = '2099-01-05' AND hora = '09:00:00' AND status != 'Cancelado'")->fetchColumn() === 0, 'Horario liberado');
conferir(!excluirContaCliente($db, 1, 'Teste123'), 'Reenvio nao exclui outra conta');

// Simula falha depois de remover agendamentos, verificando a reversao completa.
$db->exec('ALTER TABLE recuperacao_senha CHANGE id_cliente coluna_invalida INT');
try {
    excluirContaCliente($db, 2, 'Teste123');
    throw new RuntimeException('Falha esperada nao ocorreu');
} catch (PDOException $e) {
    conferir(!$db->inTransaction(), 'Transacao encerrada apos falha');
    foreach (['cliente', 'agendamento', 'veiculo'] as $tabela) {
        conferir((int)$db->query("SELECT COUNT(*) FROM $tabela WHERE id_cliente = 2")->fetchColumn() === 1, "Rollback preserva $tabela");
    }
}
