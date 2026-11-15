
USE OBRA_CONTROL;

INSERT INTO usuario
(nombre, correo, password, rol, estado)
VALUES
('Carlos Mendoza', 'admin@obracontrol.com', 'admin123', 'ADMINISTRADOR', 'ACTIVO'),
('Juan Perez', 'juan@obracontrol.com', 'juan123', 'ENCARGADO', 'ACTIVO'),
('Maria Lopez', 'maria@obracontrol.com', 'maria123', 'ENCARGADO', 'ACTIVO'),
('Pedro Ramirez', 'pedro@obracontrol.com', 'pedro123', 'TRABAJADOR', 'ACTIVO'),
('Luis Flores', 'luis@obracontrol.com', 'luis123', 'TRABAJADOR', 'ACTIVO'),
('Ana Quispe', 'ana@obracontrol.com', 'ana123', 'TRABAJADOR', 'ACTIVO'),
('Roberto Vargas', 'roberto@obracontrol.com', 'roberto123', 'PROVEEDOR', 'ACTIVO'),
('Sofia Rojas', 'sofia@obracontrol.com', 'sofia123', 'PROVEEDOR', 'ACTIVO');

INSERT INTO registro
(id_usuario, tipo_usuario, estado)
VALUES
(1, 'ADMINISTRADOR', 'REGISTRADO'),
(2, 'ENCARGADO', 'REGISTRADO'),
(3, 'ENCARGADO', 'REGISTRADO'),
(4, 'TRABAJADOR', 'REGISTRADO'),
(5, 'TRABAJADOR', 'REGISTRADO'),
(6, 'TRABAJADOR', 'REGISTRADO'),
(7, 'PROVEEDOR', 'REGISTRADO'),
(8, 'PROVEEDOR', 'REGISTRADO');

INSERT INTO login
(id_usuario, ip_acceso, estado_acceso)
VALUES
(1, '192.168.1.10', 'EXITOSO'),
(2, '192.168.1.11', 'EXITOSO'),
(3, '192.168.1.12', 'EXITOSO'),
(4, '192.168.1.13', 'EXITOSO'),
(5, '192.168.1.14', 'EXITOSO');

INSERT INTO trabajador
(id_usuario, ci, telefono, especialidad, salario)
VALUES
(2, '6789012', '72012345', 'Ingeniero civil', 6500),
(3, '5432109', '72123456', 'Arquitecta', 6000),
(4, '4567890', '73012345', 'Albañil', 3500),
(5, '5678901', '74012345', 'Electricista', 4000),
(6, '6789123', '75012345', 'Plomero', 3800);

INSERT INTO proveedor
(nombre_empresa, nit, telefono, direccion)
VALUES
('Materiales El Constructor', '1020304050', '76543210', 'Av. Buenos Aires, La Paz'),
('Distribuidora Andina', '2030405060', '71234567', 'Zona Central, La Paz');

INSERT INTO proyecto
(id_encargado, nombre, ubicacion, fecha_inicio, fecha_fin, estado)
VALUES
(1, 'Construccion de vivienda familiar', 'Zona Sur, La Paz', '2026-01-10', NULL, 'EN EJECUCION'),
(2, 'Remodelacion de edificio', 'Centro, La Paz', '2026-02-01', NULL, 'EN EJECUCION'),
(1, 'Ampliacion de oficinas', 'Sopocachi, La Paz', '2026-03-15', NULL, 'PLANIFICADO');


INSERT INTO presupuesto
(id_proyecto, monto_total, fecha_elaboracion, descripcion, estado)
VALUES
(1, 150000, '2026-01-05', 'Presupuesto general de vivienda', 'VIGENTE'),
(2, 220000, '2026-01-25', 'Presupuesto de remodelacion', 'VIGENTE'),
(3, 95000, '2026-03-10', 'Presupuesto de ampliacion', 'VIGENTE');


INSERT INTO asignacion_trabajador
(id_proyecto, id_trabajador, cargo, fecha_asignacion)
VALUES
(1, 1, 'Encargado de obra', '2026-01-10'),
(1, 3, 'Albañil', '2026-01-12'),
(1, 4, 'Electricista', '2026-01-15'),
(2, 2, 'Encargada de obra', '2026-02-01'),
(2, 5, 'Plomero', '2026-02-05'),
(3, 1, 'Encargado de obra', '2026-03-15');


INSERT INTO categoria_material
(nombre, descripcion)
VALUES
('Cemento', 'Materiales para construccion y estructuras'),
('Acero', 'Barras y elementos metalicos'),
('Electricidad', 'Materiales e instalaciones electricas'),
('Plomeria', 'Materiales para instalaciones de agua'),
('Agregados', 'Arena, grava y piedra');

INSERT INTO material
(id_categoria, nombre, unidad_medida, precio_referencial)
VALUES
(1, 'Cemento Portland', 'Bolsa', 65),
(2, 'Barra de acero 12mm', 'Unidad', 85),
(3, 'Cable electrico 2.5mm', 'Metro', 4.50),
(4, 'Tuberia PVC 4 pulgadas', 'Unidad', 55),
(5, 'Arena fina', 'Metro cubico', 120),
(5, 'Grava', 'Metro cubico', 140);

INSERT INTO inventario
(id_proyecto, id_material, cantidad, stock_minimo)
VALUES
(1, 1, 50, 10),
(1, 2, 80, 15),
(1, 3, 200, 30),
(1, 5, 15, 5),
(2, 1, 30, 10),
(2, 4, 40, 10),
(2, 6, 12, 4),
(3, 1, 20, 5);

INSERT INTO compra
(id_proyecto, id_proveedor, id_usuario, fecha, total, estado)
VALUES
(1, 1, 1, '2026-01-15 10:30:00', 0, 'RECIBIDA'),
(2, 2, 1, '2026-02-10 11:00:00', 0, 'RECIBIDA'),
(1, 2, 2, '2026-03-01 09:00:00', 0, 'PENDIENTE');

INSERT INTO detalle_compra
(id_compra, id_material, cantidad, precio_unitario, subtotal)
VALUES
(1, 1, 20, 65, 1300),
(1, 2, 10, 85, 850),
(2, 3, 100, 4.50, 450),
(2, 4, 15, 55, 825),
(3, 5, 5, 120, 600);

INSERT INTO gasto
(id_proyecto, id_compra, concepto, monto, fecha, tipo)
VALUES
(1, NULL, 'Pago de mano de obra', 3500, '2026-01-30', 'MANO_OBRA'),
(1, 1, 'Compra de cemento y acero', 2150, '2026-01-15', 'MATERIAL'),
(2, NULL, 'Transporte de materiales', 450, '2026-02-12', 'TRANSPORTE'),
(2, 2, 'Compra de materiales electricos y PVC', 1275, '2026-02-10', 'MATERIAL');

INSERT INTO avance_obra
(id_proyecto, id_usuario, fecha, porcentaje, descripcion)
VALUES
(1, 2, '2026-01-20 10:00:00', 15, 'Preparacion del terreno y excavacion'),
(1, 2, '2026-02-15 10:00:00', 35, 'Construccion de cimientos y columnas'),
(1, 2, '2026-03-15 10:00:00', 55, 'Levantamiento de muros'),
(2, 3, '2026-02-20 11:00:00', 25, 'Demolicion y limpieza'),
(2, 3, '2026-03-20 11:00:00', 45, 'Remodelacion de instalaciones');
