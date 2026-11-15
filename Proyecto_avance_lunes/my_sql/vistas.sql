
CREATE OR REPLACE VIEW vw_proyectos_detalle AS
SELECT
    p.id_proyecto,
    p.nombre,
    p.ubicacion,
    p.fecha_inicio,
    p.fecha_fin,
    p.estado,
    t.id_trabajador,
    u.nombre AS encargado,
    pr.monto_total AS presupuesto
FROM proyecto p
INNER JOIN trabajador t
    ON p.id_encargado = t.id_trabajador
INNER JOIN usuario u
    ON t.id_usuario = u.id_usuario
LEFT JOIN presupuesto pr
    ON p.id_proyecto = pr.id_proyecto;

CREATE OR REPLACE VIEW vw_trabajadores_detalle AS
SELECT
    t.id_trabajador,
    u.nombre,
    u.correo,
    t.ci,
    t.telefono,
    t.especialidad,
    t.salario,
    u.estado
FROM trabajador t
INNER JOIN usuario u
    ON t.id_usuario = u.id_usuario;

CREATE OR REPLACE VIEW vw_proveedores_detalle AS
SELECT
    id_proveedor,
    nombre_empresa,
    nit,
    telefono,
    direccion
FROM proveedor;

CREATE OR REPLACE VIEW vw_materiales_detalle AS
SELECT
    m.id_material,
    m.nombre,
    c.nombre AS categoria,
    m.unidad_medida,
    m.precio_referencial
FROM material m
INNER JOIN categoria_material c
    ON m.id_categoria = c.id_categoria;

CREATE OR REPLACE VIEW vw_inventario_detalle AS
SELECT
    i.id_inventario,
    p.nombre AS proyecto,
    m.nombre AS material,
    c.nombre AS categoria,
    m.unidad_medida,
    i.cantidad,
    i.stock_minimo,
    CASE
        WHEN i.cantidad <= i.stock_minimo THEN 'STOCK BAJO'
        ELSE 'DISPONIBLE'
    END AS estado_stock
FROM inventario i
INNER JOIN proyecto p
    ON i.id_proyecto = p.id_proyecto
INNER JOIN material m
    ON i.id_material = m.id_material
INNER JOIN categoria_material c
    ON m.id_categoria = c.id_categoria;

CREATE OR REPLACE VIEW vw_compras_detalle AS
SELECT
    c.id_compra,
    p.nombre AS proyecto,
    pr.nombre_empresa AS proveedor,
    u.nombre AS registrado_por,
    c.fecha,
    c.total,
    c.estado,
    COUNT(dc.id_detalle) AS cantidad_productos
FROM compra c
INNER JOIN proyecto p
    ON c.id_proyecto = p.id_proyecto
INNER JOIN proveedor pr
    ON c.id_proveedor = pr.id_proveedor
INNER JOIN usuario u
    ON c.id_usuario = u.id_usuario
LEFT JOIN detalle_compra dc
    ON c.id_compra = dc.id_compra
GROUP BY
    c.id_compra,
    p.nombre,
    pr.nombre_empresa,
    u.nombre,
    c.fecha,
    c.total,
    c.estado;

CREATE OR REPLACE VIEW vw_gastos_detalle AS
SELECT
    g.id_gasto,
    p.nombre AS proyecto,
    g.concepto,
    g.monto,
    g.fecha,
    g.tipo,
    c.id_compra
FROM gasto g
INNER JOIN proyecto p
    ON g.id_proyecto = p.id_proyecto
LEFT JOIN compra c
    ON g.id_compra = c.id_compra;

CREATE OR REPLACE VIEW vw_presupuestos_detalle AS
SELECT
    pr.id_presupuesto,
    p.id_proyecto,
    p.nombre AS proyecto,
    p.ubicacion,
    pr.monto_total,
    pr.fecha_elaboracion,
    pr.descripcion,
    pr.estado,
    COALESCE((
        SELECT SUM(g.monto)
        FROM gasto g
        WHERE g.id_proyecto = p.id_proyecto
    ), 0) AS total_gastado,
    pr.monto_total - COALESCE((
        SELECT SUM(g.monto)
        FROM gasto g
        WHERE g.id_proyecto = p.id_proyecto
    ), 0) AS saldo_disponible
FROM presupuesto pr
INNER JOIN proyecto p
    ON pr.id_proyecto = p.id_proyecto;

CREATE OR REPLACE VIEW vw_avances_detalle AS
SELECT
    a.id_avance,
    p.nombre AS proyecto,
    u.nombre AS responsable,
    a.fecha,
    a.porcentaje,
    a.descripcion
FROM avance_obra a
INNER JOIN proyecto p
    ON a.id_proyecto = p.id_proyecto
INNER JOIN usuario u
    ON a.id_usuario = u.id_usuario;

CREATE OR REPLACE VIEW vw_resumen_dashboard AS
SELECT
    (SELECT COUNT(*) FROM proyecto) AS total_proyectos,
    (SELECT COUNT(*) FROM trabajador) AS total_trabajadores,
    (SELECT COUNT(*) FROM material) AS total_materiales,
    (SELECT COALESCE(SUM(monto), 0) FROM gasto) AS total_gastos,
    (SELECT COUNT(*) FROM proveedor) AS total_proveedores,
    (SELECT COUNT(*) FROM compra) AS total_compras;
    
    
SELECT * FROM vw_resumen_dashboard;
SELECT * FROM vw_proyectos_detalle;
SELECT * FROM vw_compras_detalle;
SELECT * FROM vw_inventario_detalle;
SELECT * FROM vw_presupuestos_detalle;