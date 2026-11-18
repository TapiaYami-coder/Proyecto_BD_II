<?php
$rol_permitido = 'TRABAJADOR';
require_once '../conexion.php';
require_once '../verificar_sesion.php';
require_once '../funciones.php';
$id_usuario = $_SESSION['id_usuario'];
$mensaje = '';
$error = '';
$proyectos = $conn->prepare('SELECT p.id_proyecto,p.nombre FROM proyecto p INNER JOIN asignacion_trabajador a ON p.id_proyecto=a.id_proyecto INNER JOIN trabajador t ON a.id_trabajador=t.id_trabajador WHERE t.id_usuario=? ORDER BY p.nombre');
$proyectos->bind_param('i', $id_usuario);
$proyectos->execute();
$lista = $proyectos->get_result();
if (isset($_POST['registrar'])) {
    $id_proyecto = $_POST['id_proyecto'] ?? '';
    $porcentaje = $_POST['porcentaje'] ?? '';
    $descripcion = trim($_POST['descripcion'] ?? '');
    $verifica = $conn->prepare('SELECT p.id_proyecto FROM proyecto p INNER JOIN asignacion_trabajador a ON p.id_proyecto=a.id_proyecto INNER JOIN trabajador t ON a.id_trabajador=t.id_trabajador WHERE p.id_proyecto=? AND t.id_usuario=?');
    $verifica->bind_param('ii', $id_proyecto, $id_usuario);
    $verifica->execute();
    if (!$verifica->get_result()->num_rows)
        $error = 'Solo puedes registrar avances en proyectos donde estás asignado.';
    elseif ($porcentaje < 0 || $porcentaje > 100 || $descripcion === '')
        $error = 'Verifica el porcentaje y la descripción.';
    else {
        $st = $conn->prepare('INSERT INTO avance_obra(id_proyecto,id_usuario,porcentaje,descripcion) VALUES(?,?,?,?)');
        $st->bind_param('iids', $id_proyecto, $id_usuario, $porcentaje, $descripcion);
        if ($st->execute())
            $mensaje = 'Tu avance fue registrado correctamente.';
        else
            $error = 'No se pudo registrar el avance.';
    }
}
$consulta = $conn->prepare('SELECT a.id_avance,p.nombre proyecto,a.fecha,a.porcentaje,a.descripcion FROM avance_obra a INNER JOIN proyecto p ON a.id_proyecto=p.id_proyecto WHERE a.id_usuario=? ORDER BY a.fecha DESC');
$consulta->bind_param('i', $id_usuario);
$consulta->execute();
$resultado = $consulta->get_result();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis avances | OBRA CONTROL</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body><?php include '../encabezado.php';
include '../menu.php'; ?>
    <section class="titulo-pagina"><span class="sobre-titulo">ÁREA DE TRABAJO</span>
        <h2 class="titulo-seccion">Mis avances de obra</h2>
        <p>Registra el progreso de los proyectos en los que estás asignado y consulta tu historial.</p>
    </section>
    <?php if ($mensaje)
        echo '<div class="alerta">' . htmlspecialchars($mensaje) . '</div>';
    if ($error)
        echo '<div class="alerta error">' . htmlspecialchars($error) . '</div>'; ?>
    <section class="tarjeta">
        <div class="encabezado-tabla">
            <div>
                <h3 class="titulo-tarjeta">Registrar nuevo avance</h3>
                <p>El porcentaje debe representar el avance reportado para este registro.</p>
            </div><span class="etiqueta">Nuevo registro</span>
        </div>
        <form method="POST" class="rejilla-formulario">
            <div><label class="etiqueta-campo">Proyecto asignado</label><select name="id_proyecto" required>
                    <option value="">Seleccionar proyecto</option><?php while ($p = $lista->fetch_assoc()) { ?>
                        <option value="<?php echo $p['id_proyecto']; ?>"><?php echo htmlspecialchars($p['nombre']); ?>
                        </option><?php } ?>
                </select></div>
            <div><label class="etiqueta-campo">Porcentaje (%)</label><input type="number" name="porcentaje" min="0"
                    max="100" step="0.01" required></div>
            <div class="campo-completo"><label class="etiqueta-campo">Descripción del trabajo realizado</label><textarea
                    name="descripcion" rows="3" maxlength="250" required
                    placeholder="Describe las actividades realizadas..."></textarea></div>
            <div class="campo-completo"><button class="boton-principal" name="registrar">Registrar avance</button></div>
        </form>
    </section>
    <section class="tarjeta">
        <div class="encabezado-tabla">
            <h3 class="titulo-tarjeta">Historial de mis avances</h3><span class="etiqueta">Mi actividad</span>
        </div><?php mostrar_tabla($resultado); ?>
    </section>
    <section class="imagen-destacada">
        <div>
            <h3 class="titulo-tarjeta">Cada avance cuenta</h3>
            <p>Registra tus actividades para mantener actualizado el progreso de la obra.</p>
        </div>
    </section>
    <?php include '../menu_final.php'; ?>
</body>

</html>