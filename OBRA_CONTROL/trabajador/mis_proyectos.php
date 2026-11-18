<?php
$rol_permitido = "TRABAJADOR";
require_once "../conexion.php";
require_once "../verificar_sesion.php";
require_once "../funciones.php";
$sql = "SELECT p.* FROM vw_proyectos_detalle p INNER JOIN asignacion_trabajador a ON p.id_proyecto=a.id_proyecto INNER JOIN trabajador t ON a.id_trabajador=t.id_trabajador WHERE t.id_usuario=" . $_SESSION["id_usuario"] . "";
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis proyectos - OBRA CONTROL</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body>
    <?php include "../encabezado.php";
    include "../menu.php"; ?>
    <section class="titulo-pagina">
        <h2 class="titulo-seccion">Mis proyectos</h2>
        <p>Consulta y administra la informacion del modulo.</p>
    </section>
    <section class="tarjeta">
        <div class="encabezado-tabla">
            <h3 class="titulo-tarjeta">Informacion registrada</h3><span class="etiqueta">Datos de MySQL</span>
        </div><?php mostrar_tabla($resultado); ?>
    </section>
    <section class="tarjeta espacio-imagen">
        <h3 class="titulo-tarjeta">Mis proyectos</h3>
        <p>Espacio reservado para imagen del modulo.</p>
    </section>
    <?php include "../menu_final.php"; ?>
</body>

</html>