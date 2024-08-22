/* Select fecha maxima */
SELECT *
FROM plataforma_ventas_temp
WHERE fecha = (
    SELECT MAX(fecha)
    FROM plataforma_ventas_temp
    WHERE id_syscom = 176065
)
AND id_syscom = 176065;

