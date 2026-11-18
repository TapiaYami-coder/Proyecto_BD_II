<?php
$rol_permitido = 'PROVEEDOR';
require_once '../conexion.php';
require_once '../verificar_sesion.php';
require_once '../funciones.php';
$id = $_SESSION['id_usuario'];
$st = $conn->prepare('SELECT p.id_proyecto,p.nombre,p.ubicacion,p.fecha_inicio,p.fecha_fin,p.estado,COUNT(c.id_compra) compras,SUM(c.total) total_compras FROM compra c INNER JOIN proyecto p ON c.id_proyecto=p.id_proyecto INNER JOIN proveedor pr ON c.id_proveedor=pr.id_proveedor WHERE pr.id_usuario=? GROUP BY p.id_proyecto,p.nombre,p.ubicacion,p.fecha_inicio,p.fecha_fin,p.estado ORDER BY total_compras DESC');
$st->bind_param('i', $id);
$st->execute();
$resultado = $st->get_result();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Proyectos atendidos | OBRA CONTROL</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body>
    <?php include '../encabezado.php';
    include '../menu.php'; ?>
    <section class="titulo-pagina"><span class="sobre-titulo">ÁREA DEL PROVEEDOR</span>
        <h2 class="titulo-seccion">Proyectos que atiendo</h2>
        <p>Obras en las que tu empresa ha participado mediante compras registradas.</p>
    </section>
    <section class="tarjeta">
        <div class="encabezado-tabla">
            <div>
                <h3 class="titulo-tarjeta">Obras atendidas</h3>
                <p>Número de pedidos y monto total por proyecto.</p>
            </div><span class="etiqueta">Mis clientes</span>
        </div><?php mostrar_tabla($resultado); ?>
    </section>
    <section class="imagen-destacada">
        <div>
            <h3 class="titulo-tarjeta">Presencia en cada obra</h3>
            <p>Conoce los proyectos donde tus suministros han estado presentes.</p>
        </div>
    </section>
    <?php include '../menu_final.php'; ?>
</body>

</html>