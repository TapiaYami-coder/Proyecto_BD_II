<?php
$rol_permitido = 'PROVEEDOR';
require_once '../conexion.php';
require_once '../verificar_sesion.php';
require_once '../funciones.php';
$id = $_SESSION['id_usuario'];
$st = $conn->prepare('SELECT m.id_material,m.nombre material,c.nombre categoria,m.unidad_medida,SUM(dc.cantidad) total_suministrado,SUM(dc.subtotal) total_facturado FROM detalle_compra dc INNER JOIN compra co ON dc.id_compra=co.id_compra INNER JOIN material m ON dc.id_material=m.id_material INNER JOIN categoria_material c ON m.id_categoria=c.id_categoria INNER JOIN proveedor pr ON co.id_proveedor=pr.id_proveedor WHERE pr.id_usuario=? GROUP BY m.id_material,m.nombre,c.nombre,m.unidad_medida ORDER BY total_facturado DESC');
$st->bind_param('i', $id);
$st->execute();
$resultado = $st->get_result();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis materiales | OBRA CONTROL</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body>
    <?php include '../encabezado.php';
    include '../menu.php'; ?>
    <section class="titulo-pagina"><span class="sobre-titulo">ÁREA DEL PROVEEDOR</span>
        <h2 class="titulo-seccion">Materiales que suministro</h2>
        <p>Totales acumulados por material en todas tus compras registradas.</p>
    </section>
    <section class="tarjeta">
        <div class="encabezado-tabla">
            <div>
                <h3 class="titulo-tarjeta">Materiales entregados</h3>
                <p>Totales calculados desde el detalle de compras.</p>
            </div><span class="etiqueta">Histórico</span>
        </div><?php mostrar_tabla($resultado); ?>
    </section>
    <section class="imagen-destacada">
        <div>
            <h3 class="titulo-tarjeta">Tu catálogo en obra</h3>
            <p>Estos materiales forman parte de los pedidos atendidos a proyectos.</p>
        </div>
    </section>
    <?php include '../menu_final.php'; ?>
</body>

</html>