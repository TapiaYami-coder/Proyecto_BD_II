<?php
session_start();
require_once "conexion.php";
$mensaje = "";
if (isset($_POST["ingresar"])) {
    $correo = trim($_POST["correo"]);
    $clave = $_POST["contrasena"];
    $consulta = $conn->prepare("SELECT * FROM usuario WHERE correo=? LIMIT 1");
    $consulta->bind_param("s", $correo);
    $consulta->execute();
    $resultado = $consulta->get_result();
    if ($resultado && $resultado->num_rows == 1) {
        $u = $resultado->fetch_assoc();
        $valida = password_verify($clave, $u["password"]);
        if (!$valida && hash_equals($u["password"], $clave)) {
            $hash = password_hash($clave, PASSWORD_DEFAULT);
            $act = $conn->prepare("UPDATE usuario SET password=? WHERE id_usuario=?");
            $act->bind_param("si", $hash, $u["id_usuario"]);
            $act->execute();
            $valida = true;
        }
        if ($valida && $u["estado"] == "ACTIVO") {
            $_SESSION["id_usuario"] = $u["id_usuario"];
            $_SESSION["nombre"] = $u["nombre"];
            $_SESSION["rol"] = $u["rol"];
            $ip = $_SERVER["REMOTE_ADDR"] ?? "";
            $log = $conn->prepare("INSERT INTO login(id_usuario,ip_acceso,estado_acceso) VALUES(?,?,'EXITOSO')");
            $log->bind_param("is", $u["id_usuario"], $ip);
            $log->execute();
            $carpeta = $u["rol"] == "ADMINISTRADOR" ? "administrador" : ($u["rol"] == "ENCARGADO" ? "encargado" : ($u["rol"] == "TRABAJADOR" ? "trabajador" : "proveedor"));
            header("Location: $carpeta/inicio.php");
            exit;
        }
    }
    $mensaje = "Correo o contraseña incorrectos, o cuenta bloqueada.";
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Ingresar | OBRA CONTROL</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="css/login.css">
</head>

<body class="fondo-acceso">
    <main class="tarjeta-acceso">
        <section class="panel-acceso"><span class="marca">OBRA CONTROL</span>
            <h1 class="titulo-marca">Construye, controla y avanza.</h1>
            <p>Gestiona tus proyectos de construcción desde un solo lugar.</p>
        </section>
        <section class="caja-acceso">
            <div class="logo-redondo">OC</div>
            <h2 class="titulo-seccion">Bienvenido</h2>
            <p class="texto-suave">Ingresa a tu cuenta para continuar</p><?php if ($mensaje != "") { ?>
                <div class="alerta error"><?php echo htmlspecialchars($mensaje); ?></div><?php } ?>
            <form method="POST"><label class="etiqueta-campo">Correo electrónico</label><input type="email"
                    name="correo" id="correo" placeholder="correo@ejemplo.com" required><label
                    class="etiqueta-campo">Contraseña</label><input type="password" name="contrasena" id="contrasena"
                    placeholder="Tu contraseña" required><button class="boton-principal" type="submit"
                    name="ingresar">Iniciar sesión</button></form>
            <div class="accesos-rapidos">
                <h3 class="titulo-tarjeta">Acceso rápido · Datos demo</h3>
                <p>Selecciona un perfil para completar sus datos.</p>
                <div class="rejilla-accesos"><button type="button"
                        onclick="demo('admin@obracontrol.com','admin123')">Administrador</button><button type="button"
                        onclick="demo('juan@obracontrol.com','juan123')">Encargado</button><button type="button"
                        onclick="demo('pedro@obracontrol.com','pedro123')">Trabajador</button><button type="button"
                        onclick="demo('roberto@obracontrol.com','roberto123')">Proveedor</button></div>
                <p class="enlace-registro">¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
            </div>
        </section>
    </main>
    <script>function demo(c, p) { document.getElementById('correo').value = c; document.getElementById('contrasena').value = p; }</script>
</body>

</html>