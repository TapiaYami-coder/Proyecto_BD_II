<?php
    $host = "localhost";
    $user = "root";
    $password = "1234";
    $db = "OBRA_CONTROL";
    $puerto = 3306;

    // Crear conexion al servidor
    $conn = new mysqli($host, $user, $password, $db, $puerto);

    // Verificar si hay conexion
    if($conn->connect_error){
        die("Error de conexion: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
?>