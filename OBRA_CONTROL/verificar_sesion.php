<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol'])) {
    header('Location: ../login.php');
    exit;
}
if (isset($rol_permitido) && $_SESSION['rol'] !== $rol_permitido) {
    http_response_code(403);
    exit('No tienes permiso para acceder a esta página.');
}
?>