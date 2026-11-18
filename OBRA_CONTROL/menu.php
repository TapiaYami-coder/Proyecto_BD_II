<?php
$rol = $_SESSION["rol"];
$modulos = [];
$extras = [];
if ($rol == "ADMINISTRADOR") {
    $modulos = ['proyectos.php' => 'Proyectos', 'trabajadores.php' => 'Trabajadores', 'materiales.php' => 'Materiales', 'inventario.php' => 'Inventario', 'compras.php' => 'Compras', 'reportes.php' => 'Reportes'];
    $extras = ['presupuestos.php' => 'Presupuestos', 'gastos.php' => 'Gastos', 'avances.php' => 'Avances de obra', 'proveedores.php' => 'Proveedores', 'usuarios.php' => 'Usuarios'];
} elseif ($rol == "ENCARGADO") {
    $modulos = ['proyectos.php' => 'Proyectos', 'trabajadores.php' => 'Trabajadores', 'materiales.php' => 'Materiales', 'inventario.php' => 'Inventario', 'avances.php' => 'Avances de obra', 'reportes.php' => 'Reportes'];
    $extras = ['compras.php' => 'Compras', 'presupuestos.php' => 'Presupuestos', 'gastos.php' => 'Gastos'];
} elseif ($rol == "TRABAJADOR") {
    $modulos = ['mis_proyectos.php' => 'Mis proyectos', 'mis_avances.php' => 'Mis avances', 'inventario.php' => 'Inventario de obras', 'presupuestos.php' => 'Presupuestos de obras', 'materiales.php' => 'Mis materiales', 'mi_ficha.php' => 'Mi ficha'];
} else {
    $modulos = ['mis_compras.php' => 'Mis compras', 'detalle_compras.php' => 'Detalle de compras', 'materiales.php' => 'Mis materiales', 'proyectos.php' => 'Proyectos atendidos', 'mis_datos.php' => 'Mis datos', 'actividad.php' => 'Mi actividad'];
}
?>
<div class="contenedor">
    <aside class="menu">
        <h3 class="titulo-menu-marca">OBRA CONTROL</h3>
        <a class="enlace-menu" href="inicio.php">Panel principal</a>
        <p class="titulo-menu">Módulos</p>
        <?php foreach ($modulos as $archivo => $etiqueta) { ?><a class="enlace-menu"
                href="<?php echo $archivo; ?>"><?php echo $etiqueta; ?></a>
        <?php } ?>
        <?php if (!empty($extras)) { ?>
            <details class="submenu">
                <summary>Más opciones</summary>
                <div class="submenu-lista"><?php foreach ($extras as $archivo => $etiqueta) { ?><a
                            href="<?php echo $archivo; ?>"><?php echo $etiqueta; ?></a><?php } ?></div>
            </details><?php } ?>
        <a class="enlace-menu enlace-salir" href="../cerrar_sesion.php">Cerrar sesión</a>
    </aside>
    <main class="contenido">