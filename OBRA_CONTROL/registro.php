<?php
require_once __DIR__ . '/conexion.php';
$mensaje = '';
$error = '';
if (isset($_POST['registrar'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $clave = $_POST['contrasena'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';
    $rol = $_POST['rol'] ?? '';
    $ci = trim($_POST['ci'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $especialidad = trim($_POST['especialidad'] ?? '');
    $salario = $_POST['salario'] ?? 0;
    $empresa = trim($_POST['empresa'] ?? '');
    $nit = trim($_POST['nit'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    if ($rol === 'PROVEEDOR')
        $telefono = trim($_POST['telefono_proveedor'] ?? '');
    if ($nombre == '' || $correo == '' || $clave == '' || $confirmar == '' || !in_array($rol, ['TRABAJADOR', 'PROVEEDOR']))
        $error = 'Completa los campos obligatorios.';
    elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL))
        $error = 'Ingresa un correo válido.';
    elseif (strlen($clave) < 6)
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    elseif ($clave !== $confirmar)
        $error = 'Las contraseñas no coinciden.';
    elseif ($rol === 'TRABAJADOR' && ($ci == '' || $telefono == '' || $especialidad == ''))
        $error = 'Completa los datos del trabajador.';
    elseif ($rol === 'PROVEEDOR' && ($empresa == '' || $nit == '' || $telefono == '' || $direccion == ''))
        $error = 'Completa los datos de la empresa proveedora.';
    else {
        $check = $conn->prepare('SELECT id_usuario FROM usuario WHERE correo=?');
        $check->bind_param('s', $correo);
        $check->execute();
        if ($check->get_result()->num_rows > 0)
            $error = 'Ese correo ya está registrado.';
        else {
            $conn->begin_transaction();
            try {
                $hash = password_hash($clave, PASSWORD_DEFAULT);
                $estado = 'ACTIVO';
                $st = $conn->prepare('INSERT INTO usuario(nombre,correo,password,rol,estado) VALUES(?,?,?,?,?)');
                $st->bind_param('sssss', $nombre, $correo, $hash, $rol, $estado);
                $st->execute();
                $id = $conn->insert_id;
                if ($rol === 'TRABAJADOR') {
                    $st = $conn->prepare('INSERT INTO trabajador(id_usuario,ci,telefono,especialidad,salario) VALUES(?,?,?,?,?)');
                    $salario_numero = (float) $salario;
                    $st->bind_param('isssd', $id, $ci, $telefono, $especialidad, $salario_numero);
                    $st->execute();
                } else {
                    $st = $conn->prepare('INSERT INTO proveedor(nombre_empresa,nit,telefono,direccion,id_usuario) VALUES(?,?,?,?,?)');
                    $st->bind_param('ssssi', $empresa, $nit, $telefono, $direccion, $id);
                    $st->execute();
                }
                $st = $conn->prepare('INSERT INTO registro(id_usuario,tipo_usuario,estado) VALUES(?,?,\'REGISTRADO\')');
                $st->bind_param('is', $id, $rol);
                $st->execute();
                $conn->commit();
                $mensaje = 'Registro completado. Ya puedes iniciar sesión.';
            } catch (Exception $e) {
                $conn->rollback();
                $error = 'No se pudo completar el registro. Verifica que CI y NIT no estén repetidos y que proveedor tenga la columna id_usuario.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear cuenta | OBRA CONTROL</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="css/login.css">
</head>

<body class="fondo-acceso">
    <main class="diseno-registro">
        <section class="intro-registro">
            <div class="logo-redondo">OC</div>
            <h1 class="titulo-marca">Únete a OBRA CONTROL</h1>
            <p>Registra tu cuenta para participar en la gestión de obras, tareas y suministros.</p>
            <div class="intro-pasos">
                <div class="paso-indicador activo" id="indicador1"><span class="paso-numero">1</span><span>Tu
                        cuenta</span></div>
                <div class="paso-linea"></div>
                <div class="paso-indicador" id="indicador2"><span class="paso-numero">2</span><span>Tu perfil</span>
                </div>
            </div>
        </section>
        <section class="formulario-registro"><a class="volver" href="login.php">← Volver al inicio de sesión</a>
            <h2 class="titulo-seccion">Crear cuenta</h2>
            <p class="texto-suave">Dos pasos rápidos y listo para comenzar.</p>
            <?php if ($mensaje) { ?>
                <section class="panel-exito">
                    <div class="icono-exito">✓</div>
                    <h3 class="titulo-tarjeta">¡Todo listo!</h3>
                    <p><?php echo htmlspecialchars($mensaje); ?></p><a class="boton-principal" href="login.php">Ir a iniciar
                        sesión</a>
                </section><?php } else { ?>
                <?php if ($error)
                    echo '<div class="alerta error animar-entrada">' . htmlspecialchars($error) . '</div>'; ?>
                <div class="barra-progreso">
                    <div class="barra-relleno" id="barraRelleno"></div>
                </div>
                <form method="POST" id="formRegistro" novalidate>
                    <div class="paso activo" id="paso1">
                        <h3 class="titulo-paso">Paso 1 de 2 · Datos de acceso</h3>
                        <div class="rejilla-registro">
                            <div class="campo"><label class="etiqueta-campo" for="nombre">Nombre completo /
                                    responsable</label><input id="nombre" name="nombre" required autocomplete="name"
                                    placeholder="Ej. Juan Pérez"
                                    value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>"><span
                                    class="mensaje-campo" id="error-nombre"></span></div>
                            <div class="campo"><label class="etiqueta-campo" for="correo">Correo electrónico</label><input
                                    id="correo" type="email" name="correo" required autocomplete="email"
                                    placeholder="correo@ejemplo.com"
                                    value="<?php echo htmlspecialchars($_POST['correo'] ?? ''); ?>"><span
                                    class="mensaje-campo" id="error-correo"></span></div>
                            <div class="campo"><label class="etiqueta-campo" for="contrasena">Contraseña</label>
                                <div class="campo-con-clave"><input id="contrasena" type="password" name="contrasena"
                                        minlength="6" required autocomplete="new-password"
                                        placeholder="Mínimo 6 caracteres"><button class="boton-ver-clave" type="button"
                                        onclick="verClave('contrasena',this)" aria-label="Mostrar contraseña">Ver</button>
                                </div>
                                <div class="medidor-fuerza">
                                    <div class="medidor-barras"><span></span><span></span><span></span><span></span></div>
                                    <small class="fuerza-texto" id="fuerzaTexto">Escribe una contraseña</small>
                                </div><span class="mensaje-campo" id="error-contrasena"></span>
                            </div>
                            <div class="campo"><label class="etiqueta-campo" for="confirmar">Confirmar contraseña</label>
                                <div class="campo-con-clave"><input id="confirmar" type="password" name="confirmar"
                                        minlength="6" required autocomplete="new-password"
                                        placeholder="Repite tu contraseña"><button class="boton-ver-clave" type="button"
                                        onclick="verClave('confirmar',this)" aria-label="Mostrar contraseña">Ver</button>
                                </div><span class="mensaje-campo" id="error-confirmar"></span>
                            </div>
                        </div>
                        <div class="acciones-paso"><button class="boton-principal" type="button"
                                id="siguientePaso">Continuar <span>→</span></button></div>
                    </div>
                    <div class="paso" id="paso2">
                        <h3 class="titulo-paso">Paso 2 de 2 · Tipo de cuenta</h3>
                        <p class="texto-suave">Elige cómo usarás OBRA CONTROL.</p>
                        <div class="tarjetas-rol"><label
                                class="tarjeta-rol <?php echo (($_POST['rol'] ?? '') === 'TRABAJADOR') ? 'seleccionada' : ''; ?>"><input
                                    type="radio" name="rol" value="TRABAJADOR" <?php echo (($_POST['rol'] ?? '') === 'TRABAJADOR') ? 'checked' : ''; ?> onchange="cambiarRol()"><span
                                    class="icono-rol">T</span><strong>Trabajador</strong><small>Registra avances y consulta
                                    tus obras asignadas.</small></label><label
                                class="tarjeta-rol <?php echo (($_POST['rol'] ?? '') === 'PROVEEDOR') ? 'seleccionada' : ''; ?>"><input
                                    type="radio" name="rol" value="PROVEEDOR" <?php echo (($_POST['rol'] ?? '') === 'PROVEEDOR') ? 'checked' : ''; ?> onchange="cambiarRol()"><span
                                    class="icono-rol">P</span><strong>Proveedor</strong><small>Administra tus compras,
                                    entregas y datos de empresa.</small></label></div><span class="mensaje-campo"
                            id="error-rol"></span>
                        <div id="datosTrabajador"
                            class="subformulario <?php echo (($_POST['rol'] ?? '') === 'TRABAJADOR') ? 'visible' : ''; ?>">
                            <h3 class="titulo-tarjeta">Datos del trabajador</h3>
                            <div class="rejilla-registro">
                                <div class="campo"><label class="etiqueta-campo">Carnet de identidad</label><input name="ci"
                                        value="<?php echo htmlspecialchars($_POST['ci'] ?? ''); ?>"><span
                                        class="mensaje-campo" id="error-ci"></span></div>
                                <div class="campo"><label class="etiqueta-campo">Teléfono</label><input name="telefono"
                                        value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>"><span
                                        class="mensaje-campo" id="error-telefono"></span></div>
                                <div class="campo"><label class="etiqueta-campo">Especialidad</label><input
                                        name="especialidad" placeholder="Albañil, electricista..."
                                        value="<?php echo htmlspecialchars($_POST['especialidad'] ?? ''); ?>"><span
                                        class="mensaje-campo" id="error-especialidad"></span></div>
                                <div class="campo"><label class="etiqueta-campo">Salario (Bs)</label><input type="number"
                                        min="0" step="0.01" name="salario"
                                        value="<?php echo htmlspecialchars($_POST['salario'] ?? '0'); ?>"></div>
                            </div>
                        </div>
                        <div id="datosProveedor"
                            class="subformulario <?php echo (($_POST['rol'] ?? '') === 'PROVEEDOR') ? 'visible' : ''; ?>">
                            <h3 class="titulo-tarjeta">Datos del proveedor</h3>
                            <div class="rejilla-registro">
                                <div class="campo"><label class="etiqueta-campo">Nombre de la empresa</label><input
                                        name="empresa"
                                        value="<?php echo htmlspecialchars($_POST['empresa'] ?? ''); ?>"><span
                                        class="mensaje-campo" id="error-empresa"></span></div>
                                <div class="campo"><label class="etiqueta-campo">NIT</label><input name="nit"
                                        value="<?php echo htmlspecialchars($_POST['nit'] ?? ''); ?>"><span
                                        class="mensaje-campo" id="error-nit"></span></div>
                                <div class="campo"><label class="etiqueta-campo">Teléfono</label><input
                                        name="telefono_proveedor"
                                        value="<?php echo htmlspecialchars($_POST['telefono_proveedor'] ?? ''); ?>"><span
                                        class="mensaje-campo" id="error-telefono_proveedor"></span></div>
                                <div class="campo campo-completo"><label class="etiqueta-campo">Dirección</label><input
                                        name="direccion"
                                        value="<?php echo htmlspecialchars($_POST['direccion'] ?? ''); ?>"><span
                                        class="mensaje-campo" id="error-direccion"></span></div>
                            </div>
                        </div>
                        <p class="nota-seguridad">Tu información se guarda de forma segura en la base de datos.</p>
                        <div class="acciones-paso"><button class="boton boton-secundario" type="button" id="atrasPaso">←
                                Atrás</button><button class="boton-principal" name="registrar" type="submit">Crear cuenta
                                <span>✓</span></button></div>
                    </div>
                </form><?php } ?>
        </section>
    </main>
    <script>
        function verClave(id, boton) { const c = document.getElementById(id); const ver = c.type === 'password'; c.type = ver ? 'text' : 'password'; boton.textContent = ver ? 'Ocultar' : 'Ver'; }
        function mostrarError(id, texto) { const e = document.getElementById('error-' + id); if (e) { e.textContent = texto || ''; e.classList.toggle('visible', !!texto); } const campo = document.getElementById(id); if (campo) campo.classList.toggle('invalido', !!texto); }
        function actualizarFortaleza() { const v = document.getElementById('contrasena').value; let puntos = 0; if (v.length >= 6) puntos++; if (v.length >= 10) puntos++; if (/[A-Z]/.test(v) && /[a-z]/.test(v)) puntos++; if (/[0-9]/.test(v) || /[^A-Za-z0-9]/.test(v)) puntos++; const barras = document.querySelectorAll('.medidor-barras span'); barras.forEach((b, i) => { b.className = i < puntos ? 'activa' : ''; }); const texto = ['Escribe una contraseña', 'Débil', 'Aceptable', 'Buena', 'Excelente']; document.getElementById('fuerzaTexto').textContent = v ? texto[puntos] : texto[0]; document.getElementById('fuerzaTexto').className = 'fuerza-texto nivel-' + puntos; return puntos; }
        function irPaso(n) { document.getElementById('paso1').classList.toggle('activo', n === 1); document.getElementById('paso2').classList.toggle('activo', n === 2); document.getElementById('indicador1').classList.toggle('activo', n === 1); document.getElementById('indicador2').classList.toggle('activo', n === 2); document.getElementById('barraRelleno').style.width = n === 1 ? '50%' : '100%'; document.querySelector('.formulario-registro').scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        function cambiarRol() { const r = document.querySelector('input[name="rol"]:checked'); const valor = r ? r.value : ''; document.querySelectorAll('.tarjeta-rol').forEach(t => t.classList.toggle('seleccionada', t.querySelector('input').value === valor)); const trab = document.getElementById('datosTrabajador'); const prov = document.getElementById('datosProveedor'); trab.classList.toggle('visible', valor === 'TRABAJADOR'); prov.classList.toggle('visible', valor === 'PROVEEDOR'); document.querySelectorAll('#datosTrabajador input').forEach(x => { x.required = valor === 'TRABAJADOR' && ['ci', 'telefono', 'especialidad'].includes(x.name); }); document.querySelectorAll('#datosProveedor input').forEach(x => { x.required = valor === 'PROVEEDOR'; }); mostrarError('rol', ''); }
        function validarCampo(id) { const c = document.getElementById(id); if (!c) return true; const v = c.value.trim(); let msj = ''; if (id === 'nombre' && v.length < 3) msj = 'Escribe tu nombre completo.'; if (id === 'correo' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) msj = 'Ingresa un correo válido.'; if (id === 'contrasena' && v.length < 6) msj = 'Mínimo 6 caracteres.'; if (id === 'confirmar' && v !== document.getElementById('contrasena').value) msj = 'Las contraseñas no coinciden.'; mostrarError(id, msj); return !msj; }
        function validarPaso2() { const r = document.querySelector('input[name="rol"]:checked'); if (!r) { mostrarError('rol', 'Selecciona un tipo de cuenta.'); return false; } const prefijo = r.value === 'TRABAJADOR' ? 'datosTrabajador' : 'datosProveedor'; const obligatorios = r.value === 'TRABAJADOR' ? ['ci', 'telefono', 'especialidad'] : ['empresa', 'nit', 'telefono_proveedor', 'direccion']; let ok = true; obligatorios.forEach(n => { const c = document.querySelector('#' + prefijo + ' [name="' + n + '"]'); if (c && !c.value.trim()) { mostrarError(n, 'Este campo es obligatorio.'); ok = false; } else mostrarError(n, ''); }); return ok; }
        document.addEventListener('DOMContentLoaded', function () { actualizarFortaleza(); cambiarRol(); document.getElementById('barraRelleno').style.width = '50%';['nombre', 'correo', 'contrasena', 'confirmar'].forEach(id => { const c = document.getElementById(id); c.addEventListener('blur', () => validarCampo(id)); c.addEventListener('input', () => { mostrarError(id, ''); if (id === 'contrasena') { actualizarFortaleza(); validarCampo('confirmar'); } }); }); document.getElementById('siguientePaso').addEventListener('click', function () { const ok = ['nombre', 'correo', 'contrasena', 'confirmar'].map(validarCampo).every(Boolean); if (ok) irPaso(2); else document.querySelector('.invalido')?.focus(); }); document.getElementById('atrasPaso').addEventListener('click', () => irPaso(1)); document.querySelectorAll('[name="ci"],[name="telefono"],[name="especialidad"],[name="empresa"],[name="nit"],[name="telefono_proveedor"],[name="direccion"]').forEach(c => c.addEventListener('input', () => mostrarError(c.name, ''))); document.getElementById('formRegistro').addEventListener('submit', function (e) { if (!['nombre', 'correo', 'contrasena', 'confirmar'].map(validarCampo).every(Boolean)) { e.preventDefault(); irPaso(1); document.querySelector('.invalido')?.focus(); return; } if (!validarPaso2()) { e.preventDefault(); } }); const aviso = document.querySelector('.alerta'); if (aviso) aviso.scrollIntoView({ behavior: 'smooth', block: 'center' }); });
    </script>
</body>

</html>