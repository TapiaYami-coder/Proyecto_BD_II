DELIMITER //

DROP FUNCTION IF EXISTS fn_saldo_presupuesto //

CREATE FUNCTION fn_saldo_presupuesto(p_id_proyecto INT UNSIGNED)
RETURNS DECIMAL(10,2)
READS SQL DATA
BEGIN
    DECLARE v_presupuesto DECIMAL(10,2) DEFAULT 0;
    DECLARE v_gastos DECIMAL(10,2) DEFAULT 0;

    SELECT COALESCE(monto_total, 0)
    INTO v_presupuesto
    FROM presupuesto
    WHERE id_proyecto = p_id_proyecto;

    SELECT COALESCE(SUM(monto), 0)
    INTO v_gastos
    FROM gasto
    WHERE id_proyecto = p_id_proyecto;

    RETURN v_presupuesto - v_gastos;
END //

DELIMITER ;

SELECT fn_saldo_presupuesto(1);