<?php
$rol_permitido = "ADMINISTRADOR";
require_once "../conexion.php";
require_once "../verificar_sesion.php";
require_once "../funciones.php";
$sql = "SELECT * FROM vw_resumen_dashboard";
$resultado = $conn->query($sql);
$resumen = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel principal - OBRA CONTROL</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body>
    <?php include "../encabezado.php";
    include "../menu.php"; ?>
    <section class="titulo-pagina"><span class="sobre-titulo">PANEL DE CONTROL</span>
        <h2 class="titulo-seccion">Panel principal</h2>
        <p>Resumen general de OBRA CONTROL y accesos a todos los módulos.</p>
    </section>
    <?php mostrar_indicadores($resumen); ?>
    <?php mostrar_atajos(['Proyectos' => 'proyectos.php', 'Trabajadores' => 'trabajadores.php', 'Materiales' => 'materiales.php', 'Inventario' => 'inventario.php', 'Compras' => 'compras.php', 'Reportes' => 'reportes.php', 'Presupuestos' => 'presupuestos.php', 'Gastos' => 'gastos.php', 'Avances de obra' => 'avances.php', 'Proveedores' => 'proveedores.php', 'Usuarios' => 'usuarios.php']); ?>
    <section class="tarjeta">
        <div class="encabezado-tabla">
            <div>
                <h3 class="titulo-tarjeta">Información registrada</h3>
                <p>Datos de MySQL. Usa la búsqueda, la ordenación y la paginación.</p>
            </div><span class="etiqueta">Datos de MySQL</span>
        </div><?php mostrar_tabla($resultado); ?>
    </section>
    <section class="imagen-destacada">
        <div>
            <h3 class="titulo-tarjeta">Todo bajo control</h3>
            <p>Visualiza el estado de la obra desde un solo lugar.</p>
        </div>
    </section>
    <?php include "../menu_final.php"; ?>
</body>

</html>