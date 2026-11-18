<?php
$rol_permitido = 'PROVEEDOR';
require_once '../conexion.php';
require_once '../verificar_sesion.php';
require_once '../funciones.php';
$id = $_SESSION['id_usuario'];
$st = $conn->prepare('SELECT id_login,fecha_hora,ip_acceso,estado_acceso FROM login WHERE id_usuario=? ORDER BY fecha_hora DESC');
$st->bind_param('i', $id);
$st->execute();
$resultado = $st->get_result();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mi actividad | OBRA CONTROL</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body>
    <?php include '../encabezado.php';
    include '../menu.php'; ?>
    <section class="titulo-pagina"><span class="sobre-titulo">ÁREA DEL PROVEEDOR</span>
        <h2 class="titulo-seccion">Mi actividad</h2>
        <p>Historial de accesos de tu cuenta al sistema.</p>
    </section>
    <section class="tarjeta">
        <div class="encabezado-tabla">
            <div>
                <h3 class="titulo-tarjeta">Inicios de sesión</h3>
                <p>Fecha, dirección IP y resultado de cada acceso.</p>
            </div><span class="etiqueta">Seguridad</span>
        </div><?php mostrar_tabla($resultado); ?>
    </section>
    <section class="imagen-destacada">
        <div>
            <h3 class="titulo-tarjeta">Tu espacio seguro</h3>
            <p>Revisa periódicamente la actividad de tu cuenta.</p>
        </div>
    </section>
    <?php include '../menu_final.php'; ?>
</body>

</html>