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
$opciones_select_categoria = $conn->query("SELECT id_categoria AS id,nombre FROM categoria_material ORDER BY nombre");
if (isset($_POST['guardar'])) {
    $id_registro = (int) ($_POST['id_registro'] ?? 0);
    $valores = [$_POST['id_categoria'] ?? '', $_POST['nombre'] ?? '', $_POST['unidad_medida'] ?? '', $_POST['precio_referencial'] ?? ''];
    foreach ($valores as $indice => $valor) {
        if ($valor === '') {
            $valores[$indice] = null;
        }
    }
    if ($error === '') {
        if ($id_registro > 0) {
            $sql_guardar = 'UPDATE material SET id_categoria = ?, nombre = ?, unidad_medida = ?, precio_referencial = ? WHERE id_material = ?';
            $valores[] = $id_registro;
            $tipos = 'issdi';
        } else {
            $sql_guardar = 'INSERT INTO material (id_categoria, nombre, unidad_medida, precio_referencial) VALUES (?, ?, ?, ?)';
            $tipos = 'issd';
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
    $consulta_eliminar = $conn->prepare('DELETE FROM material WHERE id_material = ?');
    $consulta_eliminar->bind_param('i', $id_eliminar);
    if ($consulta_eliminar->execute()) {
        $mensaje = 'Registro eliminado correctamente.';
    } else {
        $error = 'No se puede eliminar porque existen datos relacionados.';
    }
}
if (isset($_GET['editar'])) {
    $id_editar = (int) $_GET['editar'];
    $consulta_editar = $conn->prepare('SELECT * FROM material WHERE id_material=?');
    $consulta_editar->bind_param('i', $id_editar);
    $consulta_editar->execute();
    $registro_editar = $consulta_editar->get_result()->fetch_assoc();
}
$resultado = $conn->query("SELECT m.id_material,m.nombre,c.nombre AS categoria,m.unidad_medida,m.precio_referencial FROM material m INNER JOIN categoria_material c ON m.id_categoria=c.id_categoria ORDER BY m.id_material DESC");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materiales | OBRA CONTROL</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body>
    <?php include '../encabezado.php';
    include '../menu.php'; ?>
    <section class="titulo-pagina"><span class="sobre-titulo">GESTIÓN DE CONSTRUCCIONES</span>
        <h2 class="titulo-seccion">Materiales</h2>
        <p>Registra, consulta y administra la información de materiales.</p>
    </section>
    <?php if ($mensaje !== '') { ?>
        <div class="alerta"><?php echo htmlspecialchars($mensaje); ?></div><?php } ?>
    <?php if ($error !== '') { ?>
        <div class="alerta error"><?php echo htmlspecialchars($error); ?></div><?php } ?>
    <section class="tarjeta">
        <div class="encabezado-tabla">
            <div>
                <h3 class="titulo-tarjeta"><?php echo $registro_editar ? 'Editar registro' : 'Registrar materiales'; ?>
                </h3>
                <p>Completa los datos y guarda los cambios.</p>
            </div><span class="etiqueta">Formulario</span>
        </div>
        <form method="POST" class="rejilla-formulario"><input type="hidden" name="id_registro"
                value="<?php echo htmlspecialchars((string) ($registro_editar['id_material'] ?? '')); ?>">
            <div><label class="etiqueta-campo">Categoría</label><select name="id_categoria" required>
                    <option value="">Seleccionar...</option>
                    <?php if ($opciones_select_categoria) {
                        while ($opcion = $opciones_select_categoria->fetch_assoc()) { ?>
                            <option value="<?php echo (int) $opcion['id']; ?>" <?php echo (($registro_editar['id_categoria'] ?? '') == $opcion['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($opcion['nombre']); ?>
                            </option><?php }
                    } ?>
                </select></div>
            <div><label class="etiqueta-campo">Nombre</label><input type="text" name="nombre"
                    value="<?php echo htmlspecialchars((string) ($registro_editar['nombre'] ?? '')); ?>" required></div>
            <div><label class="etiqueta-campo">Unidad de medida</label><input type="text" name="unidad_medida"
                    value="<?php echo htmlspecialchars((string) ($registro_editar['unidad_medida'] ?? '')); ?>" required>
            </div>
            <div><label class="etiqueta-campo">Precio referencial (Bs)</label><input type="number"
                    name="precio_referencial"
                    value="<?php echo htmlspecialchars((string) ($registro_editar['precio_referencial'] ?? '')); ?>"
                    step="0.01" min="0" required></div>
            <div class="campo-completo"><button type="submit" class="boton-principal" name="guardar">Guardar
                    registro</button> <a class="boton boton-secundario" href="materiales.php">Limpiar</a></div>
        </form>
    </section>
    <section class="tarjeta">
        <div class="encabezado-tabla">
            <div>
                <h3 class="titulo-tarjeta">Registros de materiales</h3>
                <p>Información almacenada en la base de datos.</p>
            </div><span class="etiqueta">Datos de MySQL</span>
        </div><?php mostrar_tabla($resultado); ?>
        <div class="lista-acciones">
            <?php if ($resultado && $resultado->num_rows > 0) {
                $resultado->data_seek(0);
                while ($fila_accion = $resultado->fetch_assoc()) { ?><span><a
                            href="?editar=<?php echo (int) $fila_accion['id_material']; ?>">Editar
                            #<?php echo (int) $fila_accion['id_material']; ?></a> <a class="enlace-peligro"
                            href="?eliminar=<?php echo (int) $fila_accion['id_material']; ?>"
                            onclick="return confirm('¿Eliminar este registro?')">Eliminar</a></span><?php }
            } ?></div>
    </section>
    <?php include '../menu_final.php'; ?>
</body>

</html>