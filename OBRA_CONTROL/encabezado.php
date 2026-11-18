<header class="encabezado">
    <div class="marca">
        <div class="logo">
            <img src="../img/logo.png" alt="Logo">
        </div>
        <div>
            <h1 class="titulo-marca">OBRA CONTROL</h1>
            <p>Sistema de gestion de construcciones</p>
        </div>
    </div>
    <div class="usuario-cabecera"><?php echo htmlspecialchars($_SESSION["nombre"]); ?> |
        <?php echo htmlspecialchars($_SESSION["rol"]); ?><a href="../cerrar_sesion.php">Cerrar sesion</a>
    </div>
</header>