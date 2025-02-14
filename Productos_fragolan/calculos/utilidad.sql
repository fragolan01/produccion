
/* Consulta optimizada para calcular IVA, USD TOTAL y USD COSTO */
SELECT 
    sys.id_producto_syscom AS 'ID SYSCOM', 
    MAX(ps.fecha) AS 'FECHA',
    ps.precio_descuento AS 'PRECIO',
    (ps.precio_descuento * ppv.iva) AS 'IVA',
    (ps.precio_descuento + (ps.precio_descuento * ppv.iva)) AS 'USD TOTAL',
    (ps.precio_descuento + (ps.precio_descuento * ppv.iva)) * tc.normal AS 'USD COSTO'
FROM 
    plataforma_productos_syscom sys
JOIN 
    plataforma_productos_precios_syscom ps 
    ON ps.id_producto_syscom = sys.id_producto_syscom
CROSS JOIN 
    (SELECT iva FROM plataforma_productos_variables LIMIT 1) ppv
CROSS JOIN 
    (SELECT normal FROM plataforma_productos_tipo_cambio LIMIT 1) tc
GROUP BY 
    sys.id_producto_syscom, ps.precio_descuento;



/* Obterner precio Mercado Libre por vender*/
SELECT 
    sm.producto_id, 
    sm.item_id,
    a.price
FROM 
    plataforma_productos_syscom_meli sm
JOIN
    plataforma_productos_atributos a
    ON sm.item_id = a.item_id



/* Consulta combinada para obtener datos de Mercado Libre y cálculos de SYSCOM */
SELECT 
    sm.producto_id, 
    sm.item_id,
    a.price AS 'PRECIO_MERCADO_LIBRE',
    sys.id_producto_syscom AS 'ID SYSCOM', 
    MAX(ps.fecha) AS 'FECHA',
    ps.precio_descuento AS 'PRECIO_SYSCOM',
    (ps.precio_descuento * ppv.iva) AS 'IVA',
    (ps.precio_descuento + (ps.precio_descuento * ppv.iva)) AS 'USD TOTAL',
    (ps.precio_descuento + (ps.precio_descuento * ppv.iva)) * tc.normal AS 'USD COSTO'
FROM 
    plataforma_productos_syscom_meli sm
JOIN 
    plataforma_productos_atributos a
    ON sm.item_id = a.item_id
JOIN 
    plataforma_productos_syscom sys
    ON sm.producto_id = sys.id_producto_syscom
JOIN 
    plataforma_productos_precios_syscom ps 
    ON ps.id_producto_syscom = sys.id_producto_syscom
CROSS JOIN 
    (SELECT iva FROM plataforma_productos_variables LIMIT 1) ppv
CROSS JOIN 
    (SELECT normal FROM plataforma_productos_tipo_cambio LIMIT 1) tc
GROUP BY 
    sm.producto_id, sm.item_id, a.price, sys.id_producto_syscom, ps.precio_descuento;


======================================================================================


/* Consulta optimizada para calcular IVA, USD TOTAL y USD COSTO */
SELECT 
    sys.id_producto_syscom AS 'ID SYSCOM', 
    MAX(ps.fecha) AS 'FECHA',
    ps.precio_descuento AS 'PRECIO',
    (ps.precio_descuento * ppv.iva) AS 'IVA',
    (ps.precio_descuento + (ps.precio_descuento * ppv.iva)) AS 'USD TOTAL',
    (ps.precio_descuento + (ps.precio_descuento * ppv.iva)) * tc.normal AS 'USD COSTO',
    a.price AS 'MXN PRECIO ML'
FROM 
    plataforma_productos_syscom_meli sm
JOIN
    plataforma_productos_atributos a
    ON sm.item_id = a.item_id
JOIN 
    plataforma_productos_syscom sys
    ON sm.producto_id = sys.id_producto_syscom
JOIN 
    plataforma_productos_precios_syscom ps 
    ON ps.id_producto_syscom = sys.id_producto_syscom
CROSS JOIN 
    (SELECT iva FROM plataforma_productos_variables LIMIT 1) ppv
CROSS JOIN 
    (SELECT normal FROM plataforma_productos_tipo_cambio LIMIT 1) tc
GROUP BY 
        sm.producto_id, sm.item_id, a.price, sys.id_producto_syscom, ps.precio_descuento;

==========================================================================================

