
-- DROP DATABASE OBRA_CONTROL; 
CREATE DATABASE IF NOT EXISTS OBRA_CONTROL
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE OBRA_CONTROL;

CREATE TABLE usuario (
    id_usuario INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol VARCHAR(30) NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'ACTIVO'
) ENGINE=InnoDB;

CREATE TABLE registro (
    id_registro INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    tipo_usuario VARCHAR(30) NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'REGISTRADO',

    CONSTRAINT fk_registro_usuario
    FOREIGN KEY (id_usuario)
    REFERENCES usuario(id_usuario)
) ENGINE=InnoDB;



CREATE TABLE login (
    id_login INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NOT NULL,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_acceso VARCHAR(45),
    estado_acceso VARCHAR(30) NOT NULL DEFAULT 'EXITOSO',

    CONSTRAINT fk_login_usuario
    FOREIGN KEY (id_usuario)
    REFERENCES usuario(id_usuario)
) ENGINE=InnoDB;


CREATE TABLE trabajador (
    id_trabajador INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NOT NULL UNIQUE,
    ci VARCHAR(20) NOT NULL UNIQUE,
    telefono VARCHAR(20) NOT NULL,
    especialidad VARCHAR(100) NOT NULL,
    salario DECIMAL(10,2) NOT NULL DEFAULT 0,

    CONSTRAINT chk_trabajador_salario
    CHECK (salario >= 0),

    CONSTRAINT fk_trabajador_usuario
    FOREIGN KEY (id_usuario)
    REFERENCES usuario(id_usuario)
) ENGINE=InnoDB;


CREATE TABLE proveedor (
    id_proveedor INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_empresa VARCHAR(120) NOT NULL,
    nit VARCHAR(20) NOT NULL UNIQUE,
    telefono VARCHAR(20) NOT NULL,
    direccion VARCHAR(200) NOT NULL
) ENGINE=InnoDB;



CREATE TABLE proyecto (
    id_proyecto INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_encargado INT UNSIGNED NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    ubicacion VARCHAR(200) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    estado VARCHAR(30) NOT NULL DEFAULT 'PLANIFICADO',

    CONSTRAINT chk_proyecto_fechas
    CHECK (fecha_fin IS NULL OR fecha_fin >= fecha_inicio),

    CONSTRAINT fk_proyecto_encargado
    FOREIGN KEY (id_encargado)
    REFERENCES trabajador(id_trabajador)
) ENGINE=InnoDB;

CREATE TABLE presupuesto (
    id_presupuesto INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_proyecto INT UNSIGNED NOT NULL UNIQUE,
    monto_total DECIMAL(10,2) NOT NULL,
    fecha_elaboracion DATE NOT NULL,
    descripcion VARCHAR(250),
    estado VARCHAR(30) NOT NULL DEFAULT 'VIGENTE',

    CONSTRAINT chk_presupuesto_monto
    CHECK (monto_total >= 0),

    CONSTRAINT fk_presupuesto_proyecto
    FOREIGN KEY (id_proyecto)
    REFERENCES proyecto(id_proyecto)
) ENGINE=InnoDB;

CREATE TABLE asignacion_trabajador (
    id_asignacion INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_proyecto INT UNSIGNED NOT NULL,
    id_trabajador INT UNSIGNED NOT NULL,
    cargo VARCHAR(80) NOT NULL,
    fecha_asignacion DATE NOT NULL,

    CONSTRAINT uq_asignacion_proyecto_trabajador
    UNIQUE (id_proyecto, id_trabajador),

    CONSTRAINT fk_asignacion_proyecto
    FOREIGN KEY (id_proyecto)
    REFERENCES proyecto(id_proyecto),

    CONSTRAINT fk_asignacion_trabajador
    FOREIGN KEY (id_trabajador)
    REFERENCES trabajador(id_trabajador)
) ENGINE=InnoDB;


CREATE TABLE categoria_material (
    id_categoria INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(200)
) ENGINE=InnoDB;


CREATE TABLE material (
    id_material INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_categoria INT UNSIGNED NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    unidad_medida VARCHAR(30) NOT NULL,
    precio_referencial DECIMAL(10,2) NOT NULL DEFAULT 0,

    CONSTRAINT chk_material_precio
    CHECK (precio_referencial >= 0),

    CONSTRAINT fk_material_categoria
    FOREIGN KEY (id_categoria)
    REFERENCES categoria_material(id_categoria)
) ENGINE=InnoDB;

CREATE TABLE inventario (
    id_inventario INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_proyecto INT UNSIGNED NOT NULL,
    id_material INT UNSIGNED NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL DEFAULT 0,
    stock_minimo DECIMAL(10,2) NOT NULL DEFAULT 0,

    CONSTRAINT uq_inventario_proyecto_material
    UNIQUE (id_proyecto, id_material),

    CONSTRAINT chk_inventario_cantidad
    CHECK (cantidad >= 0),

    CONSTRAINT chk_inventario_stock
    CHECK (stock_minimo >= 0),

    CONSTRAINT fk_inventario_proyecto
    FOREIGN KEY (id_proyecto)
    REFERENCES proyecto(id_proyecto),

    CONSTRAINT fk_inventario_material
    FOREIGN KEY (id_material)
    REFERENCES material(id_material)
) ENGINE=InnoDB;

CREATE TABLE compra (
    id_compra INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_proyecto INT UNSIGNED NOT NULL,
    id_proveedor INT UNSIGNED NOT NULL,
    id_usuario INT UNSIGNED NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    estado VARCHAR(30) NOT NULL DEFAULT 'PENDIENTE',

    CONSTRAINT chk_compra_total
    CHECK (total >= 0),

    CONSTRAINT fk_compra_proyecto
    FOREIGN KEY (id_proyecto)
    REFERENCES proyecto(id_proyecto),

    CONSTRAINT fk_compra_proveedor
    FOREIGN KEY (id_proveedor)
    REFERENCES proveedor(id_proveedor),

    CONSTRAINT fk_compra_usuario
    FOREIGN KEY (id_usuario)
    REFERENCES usuario(id_usuario)
) ENGINE=InnoDB;

CREATE TABLE detalle_compra (
    id_detalle INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_compra INT UNSIGNED NOT NULL,
    id_material INT UNSIGNED NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,

    CONSTRAINT uq_detalle_compra_material
    UNIQUE (id_compra, id_material),

    CONSTRAINT chk_detalle_cantidad
    CHECK (cantidad > 0),

    CONSTRAINT chk_detalle_precio
    CHECK (precio_unitario >= 0),

    CONSTRAINT chk_detalle_subtotal
    CHECK (subtotal >= 0 AND subtotal = ROUND(cantidad * precio_unitario, 2)),

    CONSTRAINT fk_detalle_compra
    FOREIGN KEY (id_compra)
    REFERENCES compra(id_compra),

    CONSTRAINT fk_detalle_material
    FOREIGN KEY (id_material)
    REFERENCES material(id_material)
) ENGINE=InnoDB;

CREATE TABLE gasto (
    id_gasto INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_proyecto INT UNSIGNED NOT NULL,
    id_compra INT UNSIGNED UNIQUE,
    concepto VARCHAR(150) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha DATE NOT NULL,
    tipo VARCHAR(50) NOT NULL,

    CONSTRAINT chk_gasto_monto
    CHECK (monto >= 0),

    CONSTRAINT fk_gasto_proyecto
    FOREIGN KEY (id_proyecto)
    REFERENCES proyecto(id_proyecto),

    CONSTRAINT fk_gasto_compra
    FOREIGN KEY (id_compra)
    REFERENCES compra(id_compra)
) ENGINE=InnoDB;

CREATE TABLE avance_obra (
    id_avance INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_proyecto INT UNSIGNED NOT NULL,
    id_usuario INT UNSIGNED NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    porcentaje DECIMAL(5,2) NOT NULL,
    descripcion VARCHAR(250) NOT NULL,

    CONSTRAINT chk_avance_porcentaje
    CHECK (porcentaje >= 0 AND porcentaje <= 100),

    CONSTRAINT fk_avance_proyecto
    FOREIGN KEY (id_proyecto)
    REFERENCES proyecto(id_proyecto),

    CONSTRAINT fk_avance_usuario
    FOREIGN KEY (id_usuario)
    REFERENCES usuario(id_usuario)
) ENGINE=InnoDB;

