<?php
$host = "localhost";
$usuario = "root";
$contrasena = "1234";
$base_datos = "OBRA_CONTROL";
$puerto = 3306;
$conn = new mysqli($host, $usuario, $contrasena, $base_datos, $puerto);
if ($conn->connect_error) {
    die("Error de conexion: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>