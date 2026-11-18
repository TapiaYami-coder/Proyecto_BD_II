<?php
function mostrar_indicadores($resultado)
{
    if (!$resultado || $resultado->num_rows == 0)
        return;
    $fila = $resultado->fetch_assoc();
    $definiciones = ['total_proyectos' => ['Proyectos', 'P'], 'total_trabajadores' => ['Trabajadores', 'T'], 'total_materiales' => ['Materiales', 'M'], 'total_gastos' => ['Gastos (Bs)', 'G'], 'total_proveedores' => ['Proveedores', 'V'], 'total_compras' => ['Compras', 'C']];
    echo "<div class='rejilla-estadisticas'>";
    foreach ($definiciones as $clave => $definicion) {
        if (isset($fila[$clave])) {
            echo "<div class='tarjeta-estadistica'><span class='icono-estadistica'>" . $definicion[1] . "</span><div><span class='valor-estadistica'>" . htmlspecialchars((string) $fila[$clave]) . "</span><span class='etiqueta-estadistica'>" . $definicion[0] . "</span></div></div>";
        }
    }
    echo "</div>";
}
function mostrar_atajos($enlaces)
{
    echo "<section class='tarjeta'><div class='encabezado-tabla'><div><h3 class='titulo-tarjeta'>Accesos rápidos</h3><p>Entra directamente al módulo que necesitas.</p></div><span class='etiqueta'>Navegación</span></div><div class='rejilla-atajos'>";
    foreach ($enlaces as $texto => $archivo) {
        echo "<a class='atajo' href='" . $archivo . "'><span class='atajo-icono'>" . substr($texto, 0, 1) . "</span>" . $texto . "</a>";
    }
    echo "</div></section>";
}
function mostrar_tabla($resultado)
{
    if (!$resultado || $resultado->num_rows == 0) {
        echo "<p class='vacio'>No hay registros para mostrar.</p>";
        return;
    }
    $campos = $resultado->fetch_fields();
    $columnas = [];
    foreach ($campos as $campo) {
        $columnas[] = $campo->name;
    }
    $filas = [];
    while ($fila = $resultado->fetch_assoc()) {
        $filas[] = $fila;
    }
    echo "<div class='modulo-tabla'><div class='herramientas-tabla'>";
    echo "<div class='busqueda-tabla'><span class='icono-buscar'>⌕</span><input type='search' class='campo-busqueda-tabla' placeholder='Buscar en la tabla...' aria-label='Buscar en la tabla'></div>";
    echo "<div class='acciones-tabla'><span class='contador-filas'><strong>" . count($filas) . "</strong> registros</span><button type='button' class='boton-exportar' onclick='exportarTabla(this)'>Exportar CSV</button></div></div>";
    echo "<div class='contenedor-tabla'><table class='tabla-datos'><thead><tr>";
    foreach ($columnas as $indice => $columna) {
        echo "<th class='cabecera-ordenable' data-columna='" . $indice . "' tabindex='0'>" . htmlspecialchars($columna) . "<span class='icono-orden'>↕</span></th>";
    }
    echo "</tr></thead><tbody>";
    foreach ($filas as $fila) {
        echo "<tr>";
        foreach ($columnas as $columna) {
            echo "<td>" . htmlspecialchars((string) ($fila[$columna] ?? "")) . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table></div>";
    echo "<div class='paginacion-tabla'><button type='button' class='boton-pagina' data-direccion='-1'>← Anterior</button><span class='indicador-pagina'></span><button type='button' class='boton-pagina' data-direccion='1'>Siguiente →</button></div></div>";
}
?>