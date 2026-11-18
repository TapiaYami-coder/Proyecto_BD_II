<?php
require_once '../conexion.php';
$rol_permitido = 'ENCARGADO';
require_once '../verificar_sesion.php';
require_once '../funciones.php';
if (!in_array($_SESSION['rol'], ['ENCARGADO'], true)) {
    http_response_code(403);
    exit('No tienes permiso para acceder a esta página.');
}
$mensaje = '';
$error = '';
$registro_editar = null;
$opciones_select_proyecto = $conn->query("SELECT id_proyecto AS id,nombre FROM proyecto ORDER BY nombre");
if (isset($_POST['guardar'])) {
    $id_registro = (int) ($_POST['id_registro'] ?? 0);
    $valores = [$_POST['id_proyecto'] ?? '', $_POST['concepto'] ?? '', $_POST['monto'] ?? '', $_POST['fecha'] ?? '', $_POST['tipo'] ?? ''];
    foreach ($valores as $indice => $valor) {
        if ($valor === '') {
            $valores[$indice] = null;
        }
    }
    if ($error === '') {
        if ($id_registro > 0) {
            $sql_guardar = 'UPDATE gasto SET id_proyecto = ?, concepto = ?, monto = ?, fecha = ?, tipo = ? WHERE id_gasto = ?';
            $valores[] = $id_registro;
            $tipos = 'isdssi';
        } else {
            $sql_guardar = 'INSERT INTO gasto (id_proyecto, concepto, monto, fecha, tipo) VALUES (?, ?, ?, ?, ?)';
            $tipos = 'isdss';
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
    http_response_code(403);
    exit('Solo el administrador puede eliminar registros.');
}
if (isset($_GET['editar'])) {
    $id_editar = (int) $_GET['editar'];
    $consulta_editar = $conn->prepare('SELECT * FROM gasto WHERE id_gasto=?');
    $consulta_editar->bind_param('i', $id_editar);
    $consulta_editar->execute();
    $registro_editar = $consulta_editar->get_result()->fetch_assoc();
}
$resultado = $conn->query("SELECT g.id_gasto,p.nombre AS proyecto,g.concepto,g.monto,g.fecha,g.tipo FROM gasto g INNER JOIN proyecto p ON g.id_proyecto=p.id_proyecto ORDER BY g.fecha DESC");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gastos | OBRA CONTROL</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body>
    <?php include '../encabezado.php';
    include '../menu.php'; ?>
    <section class="titulo-pagina"><span class="sobre-titulo">GESTIÓN DE CONSTRUCCIONES</span>
        <h2 class="titulo-seccion">Gastos</h2>
        <p>Registra, consulta y administra la información de gastos.</p>
    </section>
    <?php if ($mensaje !== '') { ?>
        <div class="alerta"><?php echo htmlspecialchars($mensaje); ?></div><?php } ?>
    <?php if ($error !== '') { ?>
        <div class="alerta error"><?php echo htmlspecialchars($error); ?></div><?php } ?>
    <section class="tarjeta">
        <div class="encabezado-tabla">
            <div>
                <h3 class="titulo-tarjeta"><?php echo $registro_editar ? 'Editar registro' : 'Registrar gastos'; ?></h3>
                <p>Completa los datos y guarda los cambios.</p>
            </div><span class="etiqueta">Formulario</span>
        </div>
        <form method="POST" class="rejilla-formulario"><input type="hidden" name="id_registro"
                value="<?php echo htmlspecialchars((string) ($registro_editar['id_gasto'] ?? '')); ?>">
            <div><label class="etiqueta-campo">Proyecto</label><select name="id_proyecto" required>
                    <option value="">Seleccionar...</option>
                    <?php if ($opciones_select_proyecto) {
                        while ($opcion = $opciones_select_proyecto->fetch_assoc()) { ?>
                            <option value="<?php echo (int) $opcion['id']; ?>" <?php echo (($registro_editar['id_proyecto'] ?? '') == $opcion['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($opcion['nombre']); ?>
                            </option><?php }
                    } ?>
                </select></div>
            <div><label class="etiqueta-campo">Concepto</label><input type="text" name="concepto"
                    value="<?php echo htmlspecialchars((string) ($registro_editar['concepto'] ?? '')); ?>" required>
            </div>
            <div><label class="etiqueta-campo">Monto (Bs)</label><input type="number" name="monto"
                    value="<?php echo htmlspecialchars((string) ($registro_editar['monto'] ?? '')); ?>" step="0.01"
                    min="0" required></div>
            <div><label class="etiqueta-campo">Fecha</label><input type="date" name="fecha"
                    value="<?php echo htmlspecialchars((string) ($registro_editar['fecha'] ?? '')); ?>" required></div>
            <div><label class="etiqueta-campo">Tipo</label><select name="tipo"
                    required><?php foreach (['MANO_OBRA', 'MATERIAL', 'TRANSPORTE', 'OTRO'] as $opcion) { ?>
                        <option value="<?php echo $opcion; ?>" <?php echo (($registro_editar['tipo'] ?? '') === $opcion) ? 'selected' : ''; ?>><?php echo $opcion; ?></option><?php } ?>
                </select></div>
            <div class="campo-completo"><button type="submit" class="boton-principal" name="guardar">Guardar
                    registro</button> <a class="boton boton-secundario" href="gastos.php">Limpiar</a></div>
        </form>
    </section>
    <section class="tarjeta">
        <div class="encabezado-tabla">
            <div>
                <h3 class="titulo-tarjeta">Registros de gastos</h3>
                <p>Información almacenada en la base de datos.</p>
            </div><span class="etiqueta">Datos de MySQL</span>
        </div><?php mostrar_tabla($resultado); ?>
        <div class="lista-acciones">
            <?php if ($resultado && $resultado->num_rows > 0) {
                $resultado->data_seek(0);
                while ($fila_accion = $resultado->fetch_assoc()) { ?><span><a
                            href="?editar=<?php echo (int) $fila_accion['id_gasto']; ?>">Editar
                            #<?php echo (int) $fila_accion['id_gasto']; ?></a></span><?php }
            } ?></div>
    </section>
    <?php include '../menu_final.php'; ?>
</body>

</html>