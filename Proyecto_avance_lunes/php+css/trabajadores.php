<?php
    require_once 'conexion.php';

    $sql = "SELECT * FROM vw_trabajadores_detalle";
    $resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trabajadores - OBRA CONTROL</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
<?php include 'menu.php'; ?>

    <section class="titulo-pagina">
        <h2>Trabajadores</h2>
        <p>Consulta la información de los trabajadores.</p>
    </section>

    <section class="tarjeta">
        <div class="encabezado-tabla">
            <h3>Información registrada</h3>
            <span class="etiqueta">Datos de MySQL</span>
        </div>

        <div class="tabla-contenedor">
            <table>
                <thead>
                    <tr>
                        <?php
                        if($resultado && $resultado->num_rows > 0){
                            $campos = $resultado->fetch_fields();
                            foreach($campos as $campo){
                                echo "<th>" . htmlspecialchars($campo->name) . "</th>";
                            }
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if($resultado && $resultado->num_rows > 0){
                        while($fila = $resultado->fetch_assoc()){
                            echo "<tr>";
                            foreach($fila as $dato){
                                echo "<td>" . htmlspecialchars($dato ?? "") . "</td>";
                            }
                            echo "</tr>";
                        }
                    }else{
                        echo "<tr><td class='vacio'>No hay registros para mostrar.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>

<?php include 'menu_final.php'; ?>
</body>
</html>
