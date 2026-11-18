<?php
$rol_permitido = 'PROVEEDOR';
require_once '../conexion.php';
require_once '../verificar_sesion.php';
$id = $_SESSION['id_usuario'];
$mensaje = '';
$error = '';
if (isset($_POST['guardar'])) {
    $empresa = trim($_POST['nombre_empresa']);
    $nit = trim($_POST['nit']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $st = $conn->prepare('UPDATE proveedor SET nombre_empresa=?,nit=?,telefono=?,direccion=? WHERE id_usuario=?');
    $st->bind_param('ssssi', $empresa, $nit, $telefono, $direccion, $id);
    if ($st->execute())
        $mensaje = 'Tus datos fueron actualizados.';
    else
        $error = 'No se pudo actualizar. Verifica que el NIT no esté registrado por otra empresa.';
}
$st = $conn->prepare('SELECT nombre_empresa,nit,telefono,direccion FROM proveedor WHERE id_usuario=?');
$st->bind_param('i', $id);
$st->execute();
$datos = $st->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis datos | OBRA CONTROL</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body><?php include '../encabezado.php';
include '../menu.php'; ?>
    <section class="titulo-pagina"><span class="sobre-titulo">PERFIL DEL PROVEEDOR</span>
        <h2 class="titulo-seccion">Mi empresa</h2>
        <p>Mantén actualizados los datos de contacto y facturación de tu empresa.</p>
    </section>
    <?php if ($mensaje)
        echo '<div class="alerta">' . htmlspecialchars($mensaje) . '</div>';
    if ($error)
        echo '<div class="alerta error">' . htmlspecialchars($error) . '</div>'; ?>
    <section class="tarjeta formulario-perfil">
        <div class="encabezado-tabla">
            <div>
                <h3 class="titulo-tarjeta">Información de la empresa</h3>
                <p>Los cambios se guardan en tu perfil.</p>
            </div><span class="etiqueta">Editable</span>
        </div><?php if ($datos) { ?>
            <form method="POST" class="rejilla-formulario">
                <div><label class="etiqueta-campo">Nombre de la empresa</label><input name="nombre_empresa" required
                        value="<?php echo htmlspecialchars($datos['nombre_empresa']); ?>"></div>
                <div><label class="etiqueta-campo">NIT</label><input name="nit" required
                        value="<?php echo htmlspecialchars($datos['nit']); ?>"></div>
                <div><label class="etiqueta-campo">Teléfono</label><input name="telefono" required
                        value="<?php echo htmlspecialchars($datos['telefono']); ?>"></div>
                <div><label class="etiqueta-campo">Dirección</label><input name="direccion" required
                        value="<?php echo htmlspecialchars($datos['direccion']); ?>"></div>
                <div class="campo-completo"><button class="boton-principal" name="guardar">Guardar cambios</button></div>
            </form><?php } else { ?>
            <p>No existe una ficha de proveedor vinculada a tu cuenta. Solicita al administrador que revise la relación en
                MySQL.</p><?php } ?>
    </section>
    <section class="imagen-destacada">
        <div>
            <h3 class="titulo-tarjeta">Tu empresa, siempre al día</h3>
            <p>Una ficha actualizada facilita la comunicación con el equipo de obra.</p>
        </div>
    </section>
    <?php include '../menu_final.php'; ?>
</body>

</html>