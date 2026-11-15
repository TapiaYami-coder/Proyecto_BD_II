USE OBRA_CONTROL;

CREATE INDEX idx_usuario_rol
ON usuario(rol);

CREATE INDEX idx_proyecto_estado
ON proyecto(estado);

CREATE INDEX idx_proyecto_fecha
ON proyecto(fecha_inicio);

CREATE INDEX idx_trabajador_especialidad
ON trabajador(especialidad);

CREATE INDEX idx_material_nombre
ON material(nombre);

CREATE INDEX idx_compra_fecha
ON compra(fecha);

CREATE INDEX idx_compra_estado
ON compra(estado);

CREATE INDEX idx_gasto_fecha
ON gasto(fecha);

CREATE INDEX idx_avance_fecha
ON avance_obra(fecha);