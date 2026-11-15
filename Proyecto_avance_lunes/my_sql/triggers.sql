DELIMITER //

DROP TRIGGER IF EXISTS trg_detalle_compra_insert //

CREATE TRIGGER trg_detalle_compra_insert
AFTER INSERT ON detalle_compra
FOR EACH ROW
BEGIN
    DECLARE v_proyecto INT UNSIGNED;

    SELECT id_proyecto
    INTO v_proyecto
    FROM compra
    WHERE id_compra = NEW.id_compra;

    UPDATE compra
    SET total = (
        SELECT COALESCE(SUM(subtotal), 0)
        FROM detalle_compra
        WHERE id_compra = NEW.id_compra
    )
    WHERE id_compra = NEW.id_compra;

    INSERT INTO inventario(
        id_proyecto,
        id_material,
        cantidad,
        stock_minimo
    )
    VALUES(
        v_proyecto,
        NEW.id_material,
        NEW.cantidad,
        0
    )
    ON DUPLICATE KEY UPDATE
        cantidad = cantidad + NEW.cantidad;
END //


DROP TRIGGER IF EXISTS trg_detalle_compra_update //

CREATE TRIGGER trg_detalle_compra_update
AFTER UPDATE ON detalle_compra
FOR EACH ROW
BEGIN
    DECLARE v_proyecto_anterior INT UNSIGNED;
    DECLARE v_proyecto_nuevo INT UNSIGNED;

    SELECT id_proyecto
    INTO v_proyecto_anterior
    FROM compra
    WHERE id_compra = OLD.id_compra;

    SELECT id_proyecto
    INTO v_proyecto_nuevo
    FROM compra
    WHERE id_compra = NEW.id_compra;

    UPDATE compra
    SET total = (
        SELECT COALESCE(SUM(subtotal), 0)
        FROM detalle_compra
        WHERE id_compra = OLD.id_compra
    )
    WHERE id_compra = OLD.id_compra;

    UPDATE compra
    SET total = (
        SELECT COALESCE(SUM(subtotal), 0)
        FROM detalle_compra
        WHERE id_compra = NEW.id_compra
    )
    WHERE id_compra = NEW.id_compra;

    IF v_proyecto_anterior = v_proyecto_nuevo
       AND OLD.id_material = NEW.id_material THEN

        UPDATE inventario
        SET cantidad = cantidad + NEW.cantidad - OLD.cantidad
        WHERE id_proyecto = v_proyecto_nuevo
          AND id_material = NEW.id_material;

    ELSE

        UPDATE inventario
        SET cantidad = cantidad - OLD.cantidad
        WHERE id_proyecto = v_proyecto_anterior
          AND id_material = OLD.id_material;

        INSERT INTO inventario(
            id_proyecto,
            id_material,
            cantidad,
            stock_minimo
        )
        VALUES(
            v_proyecto_nuevo,
            NEW.id_material,
            NEW.cantidad,
            0
        )
        ON DUPLICATE KEY UPDATE
            cantidad = cantidad + NEW.cantidad;

    END IF;
END //

DROP TRIGGER IF EXISTS trg_detalle_compra_delete //

CREATE TRIGGER trg_detalle_compra_delete
AFTER DELETE ON detalle_compra
FOR EACH ROW
BEGIN
    DECLARE v_proyecto INT UNSIGNED;

    SELECT id_proyecto
    INTO v_proyecto
    FROM compra
    WHERE id_compra = OLD.id_compra;

    UPDATE compra
    SET total = (
        SELECT COALESCE(SUM(subtotal), 0)
        FROM detalle_compra
        WHERE id_compra = OLD.id_compra
    )
    WHERE id_compra = OLD.id_compra;

    UPDATE inventario
    SET cantidad = cantidad - OLD.cantidad
    WHERE id_proyecto = v_proyecto
      AND id_material = OLD.id_material;
END //

DELIMITER ;

SHOW TRIGGERS;
