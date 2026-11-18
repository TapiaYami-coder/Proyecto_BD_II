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
$opciones_select_usuario_trabajador = $conn->query("SELECT id_usuario AS id,nombre FROM usuario WHERE rol='TRABAJADOR' ORDER BY nombre");
if (isset($_POST['guardar'])) {
    $id_registro = (int) ($_POST['id_registro'] ?? 0);
    $valores = [$_POST['id_usuario'] ?? '', $_POST['ci'] ?? '', $_POST['telefono'] ?? '', $_POST['especialidad'] ?? '', $_POST['salario'] ?? ''];
    foreach ($valores as $indice => $valor) {
        if ($valor === '') {
            $valores[$indice] = null;
        }
    }
    if ($error === '') {
        if ($id_registro > 0) {
            $sql_guardar = 'UPDATE trabajador SET id_usuario = ?, ci = ?, telefono = ?, especialidad = ?, salario = ? WHERE id_trabajador = ?';
            $valores[] = $id_registro;
            $tipos = 'isssdi';
        } else {
            $sql_guardar = 'INSERT INTO trabajador (id_usuario, ci, telefono, especialidad, salario) VALUES (?, ?, ?, ?, ?)';
            $tipos = 'isssd';
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
    $consulta_editar = $conn->prepare('SELECT * FROM trabajador WHERE id_trabajador=?');
    $consulta_editar->bind_param('i', $id_editar);
    $consulta_editar->execute();
    $registro_editar = $consulta_editar->get_result()->fetch_assoc();
}
$resultado = $conn->query("SELECT t.id_trabajador,u.nombre,u.correo,t.ci,t.telefono,t.especialidad,t.salario FROM trabajador t INNER JOIN usuario u ON t.id_usuario=u.id_usuario ORDER BY t.id_trabajador DESC");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trabajadores | OBRA CONTROL</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body>
    <?php include '../encabezado.php';
    include '../menu.php'; ?>
    <section class="titulo-pagina"><span class="sobre-titulo">GESTIÓN DE CONSTRUCCIONES</span>
        <h2 class="titulo-seccion">Trabajadores</h2>
        <p>Registra, consulta y administra la información de trabajadores.</p>
    </section>
    <?php if ($mensaje !== '') { ?>
        <div class="alerta"><?php echo htmlspecialchars($mensaje); ?></div><?php } ?>
    <?php if ($error !== '') { ?>
        <div class="alerta error"><?php echo htmlspecialchars($error); ?></div><?php } ?>
    <section class="tarjeta">
        <div class="encabezado-tabla">
            <div>
                <h3 class="titulo-tarjeta">
                    <?php echo $registro_editar ? 'Editar registro' : 'Registrar trabajadores'; ?></h3>
                <p>Completa los datos y guarda los cambios.</p>
            </div><span class="etiqueta">Formulario</span>
        </div>
        <form method="POST" class="rejilla-formulario"><input type="hidden" name="id_registro"
                value="<?php echo htmlspecialchars((string) ($registro_editar['id_trabajador'] ?? '')); ?>">
            <div><label class="etiqueta-campo">Cuenta de usuario</label><select name="id_usuario" required>
                    <option value="">Seleccionar...</option>
                    <?php if ($opciones_select_usuario_trabajador) {
                        while ($opcion = $opciones_select_usuario_trabajador->fetch_assoc()) { ?>
                            <option value="<?php echo (int) $opcion['id']; ?>" <?php echo (($registro_editar['id_usuario'] ?? '') == $opcion['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($opcion['nombre']); ?>
                            </option><?php }
                    } ?>
                </select></div>
            <div><label class="etiqueta-campo">Carnet de identidad</label><input type="text" name="ci"
                    value="<?php echo htmlspecialchars((string) ($registro_editar['ci'] ?? '')); ?>" required></div>
            <div><label class="etiqueta-campo">Teléfono</label><input type="text" name="telefono"
                    value="<?php echo htmlspecialchars((string) ($registro_editar['telefono'] ?? '')); ?>" required>
            </div>
            <div><label class="etiqueta-campo">Especialidad</label><input type="text" name="especialidad"
                    value="<?php echo htmlspecialchars((string) ($registro_editar['especialidad'] ?? '')); ?>" required>
            </div>
            <div><label class="etiqueta-campo">Salario (Bs)</label><input type="number" name="salario"
                    value="<?php echo htmlspecialchars((string) ($registro_editar['salario'] ?? '')); ?>" step="0.01"
                    min="0" required></div>
            <div class="campo-completo"><button type="submit" class="boton-principal" name="guardar">Guardar
                    registro</button> <a class="boton boton-secundario" href="trabajadores.php">Limpiar</a></div>
        </form>
    </section>
    <section class="tarjeta">
        <div class="encabezado-tabla">
            <div>
                <h3 class="titulo-tarjeta">Registros de trabajadores</h3>
                <p>Información almacenada en la base de datos.</p>
            </div><span class="etiqueta">Datos de MySQL</span>
        </div><?php mostrar_tabla($resultado); ?>
        <div class="lista-acciones">
            <?php if ($resultado && $resultado->num_rows > 0) {
                $resultado->data_seek(0);
                while ($fila_accion = $resultado->fetch_assoc()) { ?><span><a
                            href="?editar=<?php echo (int) $fila_accion['id_trabajador']; ?>">Editar
                            #<?php echo (int) $fila_accion['id_trabajador']; ?></a></span><?php }
            } ?></div>
    </section>
    <?php include '../menu_final.php'; ?>
</body>

</html>