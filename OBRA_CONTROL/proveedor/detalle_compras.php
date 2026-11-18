<?php
$rol_permitido = 'PROVEEDOR';
require_once '../conexion.php';
require_once '../verificar_sesion.php';
require_once '../funciones.php';
$id = $_SESSION['id_usuario'];
$st = $conn->prepare('SELECT c.id_compra,p.nombre proyecto,c.fecha,m.nombre material,dc.cantidad,dc.precio_unitario,dc.subtotal FROM detalle_compra dc INNER JOIN compra c ON dc.id_compra=c.id_compra INNER JOIN material m ON dc.id_material=m.id_material INNER JOIN proyecto p ON c.id_proyecto=p.id_proyecto INNER JOIN proveedor pr ON c.id_proveedor=pr.id_proveedor WHERE pr.id_usuario=? ORDER BY c.fecha DESC');
$st->bind_param('i', $id);
$st->execute();
$resultado = $st->get_result();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detalle de compras | OBRA CONTROL</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body>
    <?php include '../encabezado.php';
    include '../menu.php'; ?>
    <section class="titulo-pagina"><span class="sobre-titulo">ÁREA DEL PROVEEDOR</span>
        <h2 class="titulo-seccion">Detalle de mis compras</h2>
        <p>Materiales, cantidades y subtotales de cada pedido registrado a tu empresa.</p>
    </section>
    <section class="tarjeta">
        <div class="encabezado-tabla">
            <div>
                <h3 class="titulo-tarjeta">Líneas de pedido</h3>
                <p>Información tomada del detalle de compras.</p>
            </div><span class="etiqueta">Mis entregas</span>
        </div><?php mostrar_tabla($resultado); ?>
    </section>
    <section class="imagen-destacada">
        <div>
            <h3 class="titulo-tarjeta">Cada entrega queda registrada</h3>
            <p>Consulta el detalle para tener presente tus suministros y montos.</p>
        </div>
    </section>
    <?php include '../menu_final.php'; ?>
</body>

</html>