DELIMITER //

DROP PROCEDURE IF EXISTS sp_registrar_compra //

CREATE PROCEDURE sp_registrar_compra(
    IN p_id_proyecto INT UNSIGNED,
    IN p_id_proveedor INT UNSIGNED,
    IN p_id_usuario INT UNSIGNED
)
BEGIN
    DECLARE v_id_compra INT UNSIGNED;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    INSERT INTO compra(
        id_proyecto,
        id_proveedor,
        id_usuario,
        total,
        estado
    )
    VALUES(
        p_id_proyecto,
        p_id_proveedor,
        p_id_usuario,
        0,
        'PENDIENTE'
    );

    SET v_id_compra = LAST_INSERT_ID();

    COMMIT;

    SELECT
        v_id_compra AS id_compra,
        'COMPRA REGISTRADA' AS resultado;
END //



DROP PROCEDURE IF EXISTS sp_registrar_avance //

CREATE PROCEDURE sp_registrar_avance(
    IN p_id_proyecto INT UNSIGNED,
    IN p_id_usuario INT UNSIGNED,
    IN p_porcentaje DECIMAL(5,2),
    IN p_descripcion VARCHAR(250)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    INSERT INTO avance_obra(
        id_proyecto,
        id_usuario,
        porcentaje,
        descripcion
    )
    VALUES(
        p_id_proyecto,
        p_id_usuario,
        p_porcentaje,
        p_descripcion
    );

    COMMIT;

    SELECT 'AVANCE REGISTRADO CORRECTAMENTE' AS resultado;
END //



DROP PROCEDURE IF EXISTS sp_registrar_gasto //
CREATE PROCEDURE sp_registrar_gasto(
    IN p_id_proyecto INT UNSIGNED,
    IN p_id_compra INT UNSIGNED,
    IN p_concepto VARCHAR(150),
    IN p_monto DECIMAL(10,2),
    IN p_fecha DATE,
    IN p_tipo VARCHAR(50)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;
    INSERT INTO gasto(
        id_proyecto,
        id_compra,
        concepto,
        monto,
        fecha,
        tipo
    )
    VALUES(
        p_id_proyecto,
        p_id_compra,
        p_concepto,
        p_monto,
        p_fecha,
        p_tipo
    );
    COMMIT;
    SELECT 'GASTO REGISTRADO CORRECTAMENTE' AS resultado;
END //

DELIMITER ;

SHOW PROCEDURE STATUS
WHERE Db = 'OBRA_CONTROL';