<?php
// Guarda de sessão: só admin logado passa daqui.
// Inclua este arquivo no topo de QUALQUER página dentro de /admin.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>