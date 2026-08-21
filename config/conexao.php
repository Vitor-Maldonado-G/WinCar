<?php

$host = "localhost";
$banco = "wincar";
$usuario = "root";
$senha = "";

try {
    $conexao = new PDO(
        "mysql:host=$host;dbname=$banco;charset=utf8",
        $usuario,
        $senha
    );

    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $erro) {
    error_log("Erro na conexão com o banco: " . $erro->getMessage());
    die("Não foi possível conectar ao sistema no momento. Tente novamente em instantes.");
}

?>