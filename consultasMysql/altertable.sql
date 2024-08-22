/* Modifica valor por defecto status_meli =1  */
ALTER TABLE plataforma_ventas_temp
MODIFY COLUMN status_meli TINYINT(1) DEFAULT 1;