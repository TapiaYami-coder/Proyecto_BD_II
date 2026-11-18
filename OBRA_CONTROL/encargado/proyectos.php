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
$consulta_encargado = $conn->prepare('SELECT id_trabajador FROM trabajador WHERE id_usuario = ?');
$consulta_encargado->bind_param('i', $_SESSION['id_usuario']);
$consulta_encargado->execute();
$ficha_encargado = $consulta_encargado->get_result()->fetch_assoc();
if (isset($_POST['guardar'])) {
    $id_registro = (int) ($_POST['id_registro'] ?? 0);
    if (!$ficha_encargado) {
        $error = 'Tu cuenta no tiene una ficha de trabajador vinculada.';
    }
    $valores = [$ficha_encargado['id_trabajador'] ?? null, $_POST['nombre'] ?? '', $_POST['ubicacion'] ?? '', $_POST['fecha_inicio'] ?? '', $_POST['fecha_fin'] ?? '', $_POST['estado'] ?? ''];
    foreach ($valores as $indice => $valor) {
        if ($valor === '') {
            $valores[$indice] = null;
        }
    }
    if ($error === '') {
        if ($id_registro > 0) {
            $sql_guardar = 'UPDATE proyecto SET id_encargado = ?, nombre = ?, ubicacion = ?, fecha_inicio = ?, fecha_fin = ?, estado = ? WHERE id_proyecto = ?';
            $valores[] = $id_registro;
            $tipos = 'isssssi';
        } else {
            $sql_guardar = 'INSERT INTO proyecto (id_encargado, nombre, ubicacion, fecha_inicio, fecha_fin, estado) VALUES (?, ?, ?, ?, ?, ?)';
            $tipos = 'isssss';
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
    $consulta_editar = $conn->prepare('SELECT * FROM proyecto WHERE id_proyecto=?');
    $consulta_editar->bind_param('i', $id_editar);
    $consulta_editar->execute();
    $registro_editar = $consulta_editar->get_result()->fetch_assoc();
}
$resultado = $conn->query("SELECT p.id_proyecto,p.nombre,p.ubicacion,p.fecha_inicio,p.fecha_fin,p.estado,u.nombre AS encargado FROM proyecto p INNER JOIN trabajador t ON p.id_encargado=t.id_trabajador INNER JOIN usuario u ON t.id_usuario=u.id_usuario ORDER BY p.id_proyecto DESC");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyectos | OBRA CONTROL</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body>
    <?php include '../encabezado.php';
    include '../menu.php'; ?>
    <section class="titulo-pagina"><span class="sobre-titulo">GESTIÓN DE CONSTRUCCIONES</span>
        <h2 class="titulo-seccion">Proyectos</h2>
        <p>Registra, consulta y administra la información de proyectos.</p>
    </section>
    <?php if ($mensaje !== '') { ?>
        <div class="alerta"><?php echo htmlspecialchars($mensaje); ?></div><?php } ?>
    <?php if ($error !== '') { ?>
        <div class="alerta error"><?php echo htmlspecialchars($error); ?></div><?php } ?>
    <section class="tarjeta">
        <div class="encabezado-tabla">
            <div>
                <h3 class="titulo-tarjeta"><?php echo $registro_editar ? 'Editar registro' : 'Registrar proyectos'; ?>
                </h3>
                <p>Completa los datos y guarda los cambios.</p>
            </div><span class="etiqueta">Formulario</span>
        </div>
        <form method="POST" class="rejilla-formulario"><input type="hidden" name="id_registro"
                value="<?php echo htmlspecialchars((string) ($registro_editar['id_proyecto'] ?? '')); ?>">
            <input type="hidden" name="id_encargado"
                value="<?php echo htmlspecialchars((string) ($ficha_encargado['id_trabajador'] ?? '')); ?>">
            <div><label class="etiqueta-campo">Nombre del proyecto</label><input type="text" name="nombre"
                    value="<?php echo htmlspecialchars((string) ($registro_editar['nombre'] ?? '')); ?>" required></div>
            <div><label class="etiqueta-campo">Ubicación</label><input type="text" name="ubicacion"
                    value="<?php echo htmlspecialchars((string) ($registro_editar['ubicacion'] ?? '')); ?>" required>
            </div>
            <div><label class="etiqueta-campo">Fecha de inicio</label><input type="date" name="fecha_inicio"
                    value="<?php echo htmlspecialchars((string) ($registro_editar['fecha_inicio'] ?? '')); ?>" required>
            </div>
            <div><label class="etiqueta-campo">Fecha de finalización</label><input type="date" name="fecha_fin"
                    value="<?php echo htmlspecialchars((string) ($registro_editar['fecha_fin'] ?? '')); ?>"></div>
            <div><label class="etiqueta-campo">Estado</label><select name="estado"
                    required><?php foreach (['PLANIFICADO', 'EN EJECUCION', 'FINALIZADO', 'SUSPENDIDO'] as $opcion) { ?>
                        <option value="<?php echo $opcion; ?>" <?php echo (($registro_editar['estado'] ?? '') === $opcion) ? 'selected' : ''; ?>><?php echo $opcion; ?></option><?php } ?>
                </select></div>
            <div class="campo-completo"><button type="submit" class="boton-principal" name="guardar">Guardar
                    registro</button> <a class="boton boton-secundario" href="proyectos.php">Limpiar</a></div>
        </form>
    </section>
    <section class="tarjeta">
        <div class="encabezado-tabla">
            <div>
                <h3 class="titulo-tarjeta">Registros de proyectos</h3>
                <p>Información almacenada en la base de datos.</p>
            </div><span class="etiqueta">Datos de MySQL</span>
        </div><?php mostrar_tabla($resultado); ?>
        <div class="lista-acciones">
            <?php
            if ($resultado && $resultado->num_rows > 0) {
                $resultado->data_seek(0);

                while ($fila_accion = $resultado->fetch_assoc()) {
                    ?>
                    <a class="boton-editar" href="?editar=<?php echo (int) $fila_accion['id_proyecto']; ?>">
                        Editar #<?php echo (int) $fila_accion['id_proyecto']; ?>
                    </a>
                    <?php
                }
            }
            ?>
        </div>
    </section>
    <?php include '../menu_final.php'; ?>
</body>

</html>