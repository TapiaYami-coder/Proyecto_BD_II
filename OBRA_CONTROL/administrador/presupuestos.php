<?php
require_once '../conexion.php';
$rol_permitido = 'ADMINISTRADOR';
require_once '../verificar_sesion.php';
require_once '../funciones.php';
if (!in_array($_SESSION['rol'], ['ADMINISTRADOR'], true)) {
    http_response_code(403);
    exit('No tienes permiso para acceder a esta página.');
}
$mensaje = '';
$error = '';
$registro_editar = null;
$opciones_select_proyecto = $conn->query("SELECT id_proyecto AS id,nombre FROM proyecto ORDER BY nombre");
if (isset($_POST['guardar'])) {
    $id_registro = (int) ($_POST['id_registro'] ?? 0);
    $valores = [$_POST['id_proyecto'] ?? '', $_POST['monto_total'] ?? '', $_POST['fecha_elaboracion'] ?? '', $_POST['descripcion'] ?? '', $_POST['estado'] ?? ''];
    foreach ($valores as $indice => $valor) {
        if ($valor === '') {
            $valores[$indice] = null;
        }
    }
    if ($error === '') {
        if ($id_registro > 0) {
            $sql_guardar = 'UPDATE presupuesto SET id_proyecto = ?, monto_total = ?, fecha_elaboracion = ?, descripcion = ?, estado = ? WHERE id_presupuesto = ?';
            $valores[] = $id_registro;
            $tipos = 'idsssi';
        } else {
            $sql_guardar = 'INSERT INTO presupuesto (id_proyecto, monto_total, fecha_elaboracion, descripcion, estado) VALUES (?, ?, ?, ?, ?)';
            $tipos = 'idsss';
        }
        $consulta_guardar = $conn->prepare($sql_guardar);
        $referencias = [];
        $referencias[] = $tipos;
        foreach ($valores as $indice => &$valor) {
            $referencias[] = &$valor;
        }
        call_user_func_array([$consulta_guardar, 'bind_param'], $referencias);
        if ($consulta_guardar->execute()) {
            $mensaje = $id_registro > 0 ? 'Registro actualizado correctamente.' : 'Registro registrado correctamente.';
        } else {
            $error = 'No se pudo guardar el registro. Verifica los datos ingresados.';
        }
        unset($valor);
    }
}
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int) $_GET['eliminar'];
    $consulta_eliminar = $conn->prepare('DELETE FROM presupuesto WHERE id_presupuesto = ?');
    $consulta_eliminar->bind_param('i', $id_eliminar);
    if ($consulta_eliminar->execute()) {
        $mensaje = 'Registro eliminado correctamente.';
    } else {
        $error = 'No se puede eliminar porque existen datos relacionados.';
    }
}
if (isset($_GET['editar'])) {
    $id_editar = (int) $_GET['editar'];
    $consulta_editar = $conn->prepare('SELECT * FROM presupuesto WHERE id_presupuesto=?');
    $consulta_editar->bind_param('i', $id_editar);
    $consulta_editar->execute();
    $registro_editar = $consulta_editar->get_result()->fetch_assoc();
}
$resultado = $conn->query("SELECT pr.id_presupuesto,p.nombre AS proyecto,pr.monto_total,pr.fecha_elaboracion,pr.descripcion,pr.estado FROM presupuesto pr INNER JOIN proyecto p ON pr.id_proyecto=p.id_proyecto ORDER BY pr.id_presupuesto DESC");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presupuestos | OBRA CONTROL</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body>
    <?php include '../encabezado.php';
    include '../menu.php'; ?>
    <section class="titulo-pagina"><span class="sobre-titulo">GESTIÓN DE CONSTRUCCIONES</span>
        <h2 class="titulo-seccion">Presupuestos</h2>
        <p>Registra, consulta y administra la información de presupuestos.</p>
    </section>
    <?php if ($mensaje !== '') { ?>
        <div class="alerta"><?php echo htmlspecialchars($mensaje); ?></div><?php } ?>
    <?php if ($error !== '') { ?>
        <div class="alerta error"><?php echo htmlspecialchars($error); ?></div><?php } ?>
    <section class="tarjeta">
        <div class="encabezado-tabla">
            <div>
                <h3 class="titulo-tarjeta">
                    <?php echo $registro_editar ? 'Editar registro' : 'Registrar presupuestos'; ?></h3>
                <p>Completa los datos y guarda los cambios.</p>
            </div><span class="etiqueta">Formulario</span>
        </div>
        <form method="POST" class="rejilla-formulario"><input type="hidden" name="id_registro"
                value="<?php echo htmlspecialchars((string) ($registro_editar['id_presupuesto'] ?? '')); ?>">
            <div><label class="etiqueta-campo">Proyecto</label><select name="id_proyecto" required>
                    <option value="">Seleccionar...</option>
                    <?php if ($opciones_select_proyecto) {
                        while ($opcion = $opciones_select_proyecto->fetch_assoc()) { ?>
                            <option value="<?php echo (int) $opcion['id']; ?>" <?php echo (($registro_editar['id_proyecto'] ?? '') == $opcion['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($opcion['nombre']); ?>
                            </option><?php }
                    } ?>
                </select></div>
            <div><label class="etiqueta-campo">Monto total (Bs)</label><input type="number" name="monto_total"
                    value="<?php echo htmlspecialchars((string) ($registro_editar['monto_total'] ?? '')); ?>" step="0.01"
                    min="0" required></div>
            <div><label class="etiqueta-campo">Fecha de elaboración</label><input type="date" name="fecha_elaboracion"
                    value="<?php echo htmlspecialchars((string) ($registro_editar['fecha_elaboracion'] ?? '')); ?>"
                    required></div>
            <div><label class="etiqueta-campo">Descripción</label><input type="text" name="descripcion"
                    value="<?php echo htmlspecialchars((string) ($registro_editar['descripcion'] ?? '')); ?>" required>
            </div>
            <div><label class="etiqueta-campo">Estado</label><select name="estado"
                    required><?php foreach (['VIGENTE', 'AGOTADO', 'CERRADO'] as $opcion) { ?>
                        <option value="<?php echo $opcion; ?>" <?php echo (($registro_editar['estado'] ?? '') === $opcion) ? 'selected' : ''; ?>><?php echo $opcion; ?></option><?php } ?>
                </select></div>
            <div class="campo-completo"><button type="submit" class="boton-principal" name="guardar">Guardar
                    registro</button> <a class="boton boton-secundario" href="presupuestos.php">Limpiar</a></div>
        </form>
    </section>
    <section class="tarjeta">
        <div class="encabezado-tabla">
            <div>
                <h3 class="titulo-tarjeta">Registros de presupuestos</h3>
                <p>Información almacenada en la base de datos.</p>
            </div><span class="etiqueta">Datos de MySQL</span>
        </div><?php mostrar_tabla($resultado); ?>
        <div class="lista-acciones">
            <?php if ($resultado && $resultado->num_rows > 0) {
                $resultado->data_seek(0);
                while ($fila_accion = $resultado->fetch_assoc()) { ?><span><a
                            href="?editar=<?php echo (int) $fila_accion['id_presupuesto']; ?>">Editar
                            #<?php echo (int) $fila_accion['id_presupuesto']; ?></a> <a class="enlace-peligro"
                            href="?eliminar=<?php echo (int) $fila_accion['id_presupuesto']; ?>"
                            onclick="return confirm('¿Eliminar este registro?')">Eliminar</a></span><?php }
            } ?></div>
    </section>
    <?php include '../menu_final.php'; ?>
</body>

</html>