<?php
$rol_permitido = 'TRABAJADOR';
require_once '../conexion.php';
require_once '../verificar_sesion.php';
require_once '../funciones.php';
$id = $_SESSION['id_usuario'];
$st = $conn->prepare('SELECT v.* FROM vw_trabajadores_detalle v INNER JOIN trabajador t ON v.id_trabajador=t.id_trabajador WHERE t.id_usuario=?');
$st->bind_param('i', $id);
$st->execute();
$resultado = $st->get_result();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mi ficha | OBRA CONTROL</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body>
    <?php include '../encabezado.php';
    include '../menu.php'; ?>
    <section class="titulo-pagina"><span class="sobre-titulo">ÁREA DE TRABAJO</span>
        <h2 class="titulo-seccion">Mi ficha</h2>
        <p>Datos personales y laborales vinculados a tu cuenta.</p>
    </section>
    <section class="tarjeta">
        <div class="encabezado-tabla">
            <div>
                <h3 class="titulo-tarjeta">Información del trabajador</h3>
                <p>Si necesitas cambiar tus datos, solicítalo al administrador.</p>
            </div><span class="etiqueta">Solo lectura</span>
        </div><?php mostrar_tabla($resultado); ?>
    </section>
    <section class="imagen-destacada">
        <div>
            <h3 class="titulo-tarjeta">Tu trabajo importa</h3>
            <p>Mantén tus datos actualizados para una mejor coordinación en obra.</p>
        </div>
    </section>
    <?php include '../menu_final.php'; ?>
</body>

</html>