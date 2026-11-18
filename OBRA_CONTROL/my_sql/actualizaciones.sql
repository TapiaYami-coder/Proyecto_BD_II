USE OBRA_CONTROL;

ALTER TABLE proveedor
ADD id_usuario INT UNSIGNED UNIQUE,
ADD CONSTRAINT fk_proveedor_usuario
FOREIGN KEY (id_usuario)
REFERENCES usuario(id_usuario);

UPDATE usuario
SET rol = 'ADMINISTRADOR'
WHERE id_usuario = 1;

UPDATE usuario
SET rol = 'ENCARGADO'
WHERE id_usuario = 2;

UPDATE usuario
SET rol = 'TRABAJADOR'
WHERE id_usuario = 3;

SELECT id_usuario, nombre, correo, rol, estado
FROM usuario;

DESCRIBE trabajador;

DESCRIBE proveedor;

SELECT id_usuario, nombre, correo, rol, estado
FROM usuario;

SELECT *
FROM trabajador;

SELECT *
FROM proveedor;

USE OBRA_CONTROL;

UPDATE proveedor
SET id_usuario = 7
WHERE id_proveedor = 1;

UPDATE proveedor
SET id_usuario = 8
WHERE id_proveedor = 2;

SELECT
    p.id_proveedor,
    p.nombre_empresa,
    u.nombre AS usuario,
    u.correo,
    u.rol
FROM proveedor p
INNER JOIN usuario u
ON p.id_usuario = u.id_usuario;

USE OBRA_CONTROL;

SHOW FULL TABLES WHERE Table_type = 'VIEW';
select * from usuario;

ALTER TABLE usuario MODIFY password VARCHAR(255) NOT NULL;