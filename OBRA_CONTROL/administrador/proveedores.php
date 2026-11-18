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
if (isset($_POST['guardar'])) {
    $id_registro = (int) ($_POST['id_registro'] ?? 0);
    $valores = [$_POST['nombre_empresa'] ?? '', $_POST['nit'] ?? '', $_POST['telefono'] ?? '', $_POST['direccion'] ?? ''];
    foreach ($valores as $indice => $valor) {
        if ($valor === '') {
            $valores[$indice] = null;
        }
    }
    if ($error === '') {
        if ($id_registro > 0) {
            $sql_guardar = 'UPDATE proveedor SET nombre_empresa = ?, nit = ?, telefono = ?, direccion = ? WHERE id_proveedor = ?';
            $valores[] = $id_registro;
            $tipos = 'ssssi';
        } else {
            $sql_guardar = 'INSERT INTO proveedor (nombre_empresa, nit, telefono, direccion) VALUES (?, ?, ?, ?)';
            $tipos = 'ssss';
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
    $consulta_eliminar = $conn->prepare('DELETE FROM proveedor WHERE id_proveedor = ?');
    $consulta_eliminar->bind_param('i', $id_eliminar);
    if ($consulta_eliminar->execute()) {
        $mensaje = 'Registro eliminado correctamente.';
    } else {
        $error = 'No se puede eliminar porque existen datos relacionados.';
    }
}
if (isset($_GET['editar'])) {
    $id_editar = (int) $_GET['editar'];
    $consulta_editar = $conn->prepare('SELECT * FROM proveedor WHERE id_proveedor=?');
    $consulta_editar->bind_param('i', $id_editar);
    $consulta_editar->execute();
    $registro_editar = $consulta_editar->get_result()->fetch_assoc();
}
$resultado = $conn->query("SELECT id_proveedor,nombre_empresa,nit,telefono,direccion FROM proveedor ORDER BY id_proveedor DESC");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveedores | OBRA CONTROL</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body>
    <?php include '../encabezado.php';
    include '../menu.php'; ?>
    <section class="titulo-pagina"><span class="sobre-titulo">GESTIÓN DE CONSTRUCCIONES</span>
        <h2 class="titulo-seccion">Proveedores</h2>
        <p>Registra, consulta y administra la información de proveedores.</p>
    </section>
    <?php if ($mensaje !== '') { ?>
        <div class="alerta"><?php echo htmlspecialchars($mensaje); ?></div><?php } ?>
    <?php if ($error !== '') { ?>
        <div class="alerta error"><?php echo htmlspecialchars($error); ?></div><?php } ?>
    <section class="tarjeta">
        <div class="encabezado-tabla">
            <div>
                <h3 class="titulo-tarjeta"><?php echo $registro_editar ? 'Editar registro' : 'Registrar proveedores'; ?>
                </h3>
                <p>Completa los datos y guarda los cambios.</p>
            </div><span class="etiqueta">Formulario</span>
        </div>
        <form method="POST" class="rejilla-formulario"><input type="hidden" name="id_registro"
                value="<?php echo htmlspecialchars((string) ($registro_editar['id_proveedor'] ?? '')); ?>">
            <div><label class="etiqueta-campo">Nombre de empresa</label><input type="text" name="nombre_empresa"
                    value="<?php echo htmlspecialchars((string) ($registro_editar['nombre_empresa'] ?? '')); ?>"
                    required></div>
            <div><label class="etiqueta-campo">NIT</label><input type="text" name="nit"
                    value="<?php echo htmlspecialchars((string) ($registro_editar['nit'] ?? '')); ?>" required></div>
            <div><label class="etiqueta-campo">Teléfono</label><input type="text" name="telefono"
                    value="<?php echo htmlspecialchars((string) ($registro_editar['telefono'] ?? '')); ?>" required>
            </div>
            <div><label class="etiqueta-campo">Dirección</label><input type="text" name="direccion"
                    value="<?php echo htmlspecialchars((string) ($registro_editar['direccion'] ?? '')); ?>" required>
            </div>
            <div class="campo-completo"><button type="submit" class="boton-principal" name="guardar">Guardar
                    registro</button> <a class="boton boton-secundario" href="proveedores.php">Limpiar</a></div>
        </form>
    </section>
    <section class="tarjeta">
        <div class="encabezado-tabla">
            <div>
                <h3 class="titulo-tarjeta">Registros de proveedores</h3>
                <p>Información almacenada en la base de datos.</p>
            </div><span class="etiqueta">Datos de MySQL</span>
        </div><?php mostrar_tabla($resultado); ?>
        <div class="lista-acciones">
            <?php if ($resultado && $resultado->num_rows > 0) {
                $resultado->data_seek(0);
                while ($fila_accion = $resultado->fetch_assoc()) { ?><span><a
                            href="?editar=<?php echo (int) $fila_accion['id_proveedor']; ?>">Editar
                            #<?php echo (int) $fila_accion['id_proveedor']; ?></a> <a class="enlace-peligro"
                            href="?eliminar=<?php echo (int) $fila_accion['id_proveedor']; ?>"
                            onclick="return confirm('¿Eliminar este registro?')">Eliminar</a></span><?php }
            } ?></div>
    </section>
    <?php include '../menu_final.php'; ?>
</body>

</html>