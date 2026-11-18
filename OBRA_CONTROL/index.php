<?php
session_start();
if (isset($_SESSION["rol"])) {
    $r = $_SESSION["rol"];
    $carpeta = $r == "ADMINISTRADOR" ? "administrador" : ($r == "ENCARGADO" ? "encargado" : ($r == "TRABAJADOR" ? "trabajador" : "proveedor"));
    header("Location: " . $carpeta . "/inicio.php");
    exit;
}
header("Location: login.php");
exit;
?>