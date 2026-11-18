<?php
$rol_permitido = 'TRABAJADOR';
require_once '../conexion.php';
require_once '../verificar_sesion.php';
require_once '../funciones.php';
$id_usuario = $_SESSION['id_usuario'];
$consulta = $conn->prepare('SELECT a.id_proyecto FROM asignacion_trabajador a INNER JOIN trabajador t ON a.id_trabajador=t.id_trabajador WHERE t.id_usuario=?');
$consulta->bind_param('i', $id_usuario);
$consulta->execute();
$res = $consulta->get_result();
$ids = [];
while ($fila = $res->fetch_assoc()) {
    $ids[] = (int) $fila['id_proyecto'];
}
$lista = $ids ? implode(',', $ids) : '0';
$sql = "SELECT DISTINCT v.* FROM vw_materiales_detalle v INNER JOIN inventario i ON v.id_material=i.id_material WHERE i.id_proyecto IN ($lista) ORDER BY v.categoria, v.nombre";
$resultado = $conn->query($sql);
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
    <section class="titulo-pagina"><span class="sobre-titulo">ÁREA DE TRABAJO</span>
        <h2 class="titulo-seccion">Materiales de mis obras</h2>
        <p>Catálogo de materiales registrados en el inventario de tus proyectos asignados.</p>
    </section>
    <section class="tarjeta">
        <div class="encabezado-tabla">
            <div>
                <h3 class="titulo-tarjeta">Catálogo de materiales</h3>
                <p>Información tomada del inventario y las categorías.</p>
            </div><span class="etiqueta">Solo mis obras</span>
        </div><?php mostrar_tabla($resultado); ?>
    </section>
    <section class="imagen-destacada">
        <div>
            <h3 class="titulo-tarjeta">Materiales a la mano</h3>
            <p>Revisa las unidades y precios referenciales antes de reportar necesidades.</p>
        </div>
    </section>
    <?php include '../menu_final.php'; ?>
</body>

</html>