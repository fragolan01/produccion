<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar las dependencias necesarias
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/db/conexion.php'; // Conexión a la base de datos
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/files/token/token_meli.php'; // Clase MeliToken

// Incluir el archivo de la clase
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/calculos/temporalAcos.php';

// Crear instancia de la clase MeliToken
$meliToken = new MeliToken();

// Obtener el token
$token = $meliToken->getTokenMeli();

// Uso de la clase
$tempTableCreator = new TempTableCreator($conn); // $conn es la conexión de mysqli
$tempTableCreator->createTempTable();
$data = $tempTableCreator->fetchTempTableData();

// Crear tabla temporal para almacenar comisiones
function crearTablaTemporal($conn, $valores) {
    $sql_create_table = "
        CREATE TEMPORARY TABLE IF NOT EXISTS temp_comisiones (
            item_id VARCHAR(50) NOT NULL,
            percentage_fee DECIMAL(10, 2) DEFAULT NULL,
            PRIMARY KEY (item_id)
        )
    ";
    $conn->query($sql_create_table);

    $sql_insert = "INSERT INTO temp_comisiones (item_id, percentage_fee) VALUES (?, ?) ON DUPLICATE KEY UPDATE percentage_fee = VALUES(percentage_fee)";
    $stmt = $conn->prepare($sql_insert);

    foreach ($valores as $valor) {
        $stmt->bind_param("sd", $valor['item_id'], $valor['percentage_fee']);
        $stmt->execute();
    }
    $stmt->close();
}

// Consultar comisiones y guardar en la tabla temporal
function consultarComisionesYGuardar($valores, $token, $conn) {
    foreach ($valores as &$valor) {
        $category_id = $valor['category_id'];
        $listing_type_id = $valor['listing_type_id'];
        $price = $valor['price'];
        $item_id = $valor['item_id'];

        $url = "https://api.mercadolibre.com/sites/MLM/listing_prices?price=$price&listing_type_id=$listing_type_id&category_id=$category_id";
        $headers = [
            "Authorization: Bearer $token",
            "api-version: 2"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo "Error al realizar la solicitud: " . curl_error($ch) . "\n";
            continue;
        }

        curl_close($ch);
        $decodedResponse = json_decode($response, true);

        $valor['percentage_fee'] = $decodedResponse['sale_fee_details']['percentage_fee'] ?? null;
    }

    crearTablaTemporal($conn, $valores);
}

// Obtener datos de la base de datos
function obtenerDatos($conn) {
    $valores = [];
    $sql = "SELECT item_id, category_id, listing_type_id, price FROM plataforma_productos_atributos";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $valores[] = [
                'item_id' => $row['item_id'],
                'category_id' => $row['category_id'],
                'listing_type_id' => $row['listing_type_id'],
                'price' => $row['price']
            ];
        }
    }
    return $valores;
}

// Ejecutar las funciones
$valores = obtenerDatos($conn);
consultarComisionesYGuardar($valores, $token, $conn);

// Realizar la consulta final
$sql_utilidad = "

WITH variables AS (
    SELECT 
        (SELECT iva FROM plataforma_productos_variables LIMIT 1) AS iva,
        (SELECT fijo_ml FROM plataforma_productos_variables LIMIT 1) AS fijo_ml,
        (SELECT costo_menor_ml FROM plataforma_productos_variables LIMIT 1) AS costo_menor_ml,
        (SELECT tc.normal FROM plataforma_productos_tipo_cambio tc WHERE tc.fecha = (SELECT MAX(fecha) FROM plataforma_productos_tipo_cambio) LIMIT 1) AS tipo_cambio
),
precios_especiales AS (
    SELECT 
        ps.id_producto_syscom,
        ps.precio_descuento,
        MAX(ps.fecha) AS fecha_maxima
    FROM 
        plataforma_productos_precios_syscom ps
    GROUP BY 
        ps.id_producto_syscom, ps.precio_descuento

),
precios_calculados AS (
    SELECT 
        sm.orden,
        sm.id_prod_syscom,
        sm.id_envios,
        a.item_id, 
        a.price AS precio_ml,  
        pe.precio_descuento AS precio_descuento, -- Usamos el último precio_descuento como precio_descuento
        d.descuento,
        MAX(ps.fecha) AS fecha,
        (a.price * d.descuento / 100) AS descuento_calculado
    FROM 
        plataforma_productos_syscom_meli sm
    JOIN plataforma_productos_atributos a ON sm.id_item_meli = a.item_id
    JOIN plataforma_productos_syscom sys ON sm.id_prod_syscom = sys.id_producto_syscom
    JOIN plataforma_productos_precios_syscom ps ON ps.id_producto_syscom = sys.id_producto_syscom
    JOIN precios_especiales pe ON pe.id_producto_syscom = ps.id_producto_syscom -- Unión con la CTE para obtener el último precio_descuento
    JOIN plataforma_productos_descuento d ON d.id_producto_syscom = sm.id_prod_syscom
    GROUP BY 
        sm.orden, sm.id_prod_syscom, sm.id_envios, a.item_id, a.price, pe.precio_descuento, d.descuento
),
costo_envios AS (
    SELECT 
        sm.id_prod_syscom, 
        e.costo
    FROM 
        plataforma_productos_syscom_meli sm
    JOIN 
        plataforma_productos_envios e ON sm.id_envios = e.id
),
campañas_acos AS (
    SELECT 
        a.item_id, 
        am.campaign_id, 
        rc.nombre_campania, 
        mc.acos
    FROM 
        plataforma_productos_atributos a
    JOIN plataforma_productos_anuncio_meli am 
        ON a.item_id = am.item_id
    JOIN plataforma_productos_result_campania rc 
        ON am.campaign_id = rc.campaign_id
    JOIN plataforma_productos_metric_campania mc 
        ON rc.campaign_id = mc.id_campaign
    WHERE 
        mc.acos IS NOT NULL
)
SELECT 
    pc.orden AS 'ORDEN',
    pc.id_prod_syscom AS 'ID SYSCOM',
    pc.id_envios AS 'ID ENVIOS',
    (ce.costo * (1 + v.iva) * v.tipo_cambio) AS 'COSTO ENVÍO',
    -- Cálculo de DESCUENTO TOT
    CASE 
        WHEN pc.precio_ml < v.costo_menor_ml THEN 
            ((temp.percentage_fee / 100) * pc.precio_ml) + v.fijo_ml + ((ca.acos / 100) * (pc.precio_ml + v.fijo_ml))
        ELSE 
            ((temp.percentage_fee / 100) * pc.precio_ml) + ((ca.acos / 100) * (pc.precio_ml + v.fijo_ml))
    END AS 'DESCUENTO TOT',
    -- Cálculo de TOTAL VENTA ML
    (pc.precio_ml - pc.descuento_calculado) - 
    CASE 
        WHEN pc.precio_ml < v.costo_menor_ml THEN 
            ((temp.percentage_fee / 100) * pc.precio_ml) + v.fijo_ml + ((ca.acos / 100) * (pc.precio_ml + v.fijo_ml))
        ELSE 
            ((temp.percentage_fee / 100) * pc.precio_ml) + ((ca.acos / 100) * (pc.precio_ml + v.fijo_ml))
    END - (ce.costo * (1 + v.iva) * v.tipo_cambio) AS 'TOTAL VENTA ML',
    -- Cálculo de UTILIDAD MERCADO LIBRE
    ( -- TOTAL VENTA ML
        (pc.precio_ml - pc.descuento_calculado) - 
        CASE 
            WHEN pc.precio_ml < v.costo_menor_ml THEN 
                ((temp.percentage_fee / 100) * pc.precio_ml) + v.fijo_ml + ((ca.acos / 100) * (pc.precio_ml + v.fijo_ml))
            ELSE 
                ((temp.percentage_fee / 100) * pc.precio_ml) + ((ca.acos / 100) * (pc.precio_ml + v.fijo_ml))
        END - (ce.costo * (1 + v.iva) * v.tipo_cambio)
    ) - ( -- COSTO
        (pc.precio_descuento + (pc.precio_descuento * v.iva)) * v.tipo_cambio
    ) AS 'UTILIDAD MERCADO LIBRE',
    pc.fecha AS 'FECHA',
    ROUND((pc.precio_descuento-(pc.precio_descuento * 4/100)),2) AS 'PRECIO', -- Usamos el último precio_descuento
    ROUND((ROUND((pc.precio_descuento-(pc.precio_descuento * 4/100)),2) * v.iva),2) AS 'IVA',

    -- Aqui usar el precio_descuento - 4% de descuento
    ROUND( (ROUND ( (pc.precio_descuento - (pc.precio_descuento * 4/100) ), 2) + (ROUND((pc.precio_descuento-(pc.precio_descuento * 4/100)),2) * v.iva)) ,2) AS 'TOTAL',
    
    -- CALCULA ES COSTO ***** AQUI ***** AQUI
    -- ROUND(((pc.precio_descuento * 4/100) - pc.precio_descuento) + (( (pc.precio_descuento * 4/100) - pc.precio_descuento) * v.iva),2) * v.tipo_cambio AS 'COSTO',
    ROUND( ROUND((ROUND ( (pc.precio_descuento - (pc.precio_descuento * 4/100) ), 2) + (ROUND((pc.precio_descuento-(pc.precio_descuento * 4/100)),2) * v.iva)) ,2) * v.tipo_cambio, 2) AS 'COSTO',

    pc.precio_ml AS 'PRECIO ML',
    ROUND((temp.percentage_fee / 100) * (pc.precio_ml - pc.descuento_calculado),2) AS 'COMISIÓN',
    temp_results.ITEM AS 'ITEM',
    -- ACOS de la tabla de campañas
    (pc.precio_ml - pc.descuento_calculado) * (ca.acos / 100) AS 'ACOS_CAMPAÑA',
    -- ACOS calculado desde los datos principales
    CASE 
        WHEN pc.precio_ml < v.costo_menor_ml THEN 
            (temp_results.ACOS / 100) * ((pc.precio_ml - pc.descuento_calculado) + v.fijo_ml)
        ELSE 
            ((temp_results.ACOS / 100) * (pc.precio_ml - pc.descuento_calculado)) 
    END AS 'ACOS_CALCULADO',
    -- Precio final después de descuento
    ROUND((pc.precio_ml - pc.descuento_calculado),2) AS 'PRECIOFINAL',
    -- Porcentaje Fee
    temp.percentage_fee AS 'COMISION ML'
FROM 
    precios_calculados pc
CROSS JOIN variables v
LEFT JOIN temp_comisiones temp ON pc.item_id = temp.item_id
LEFT JOIN temp_results ON pc.item_id = temp_results.ITEM
LEFT JOIN costo_envios ce ON pc.id_prod_syscom = ce.id_prod_syscom
LEFT JOIN campañas_acos ca ON pc.item_id = ca.item_id
-- Ordenamos por la columna orden
ORDER BY pc.orden;



";

// Ejecutar la consulta
$result = $conn->query($sql_utilidad);

// Imprimir los resultados
if ($result && $result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>FECHA</th> <th>ORDEN</th> <th>ID SYSCOM</th><th>PRECIO</th><th>IVA</th><th>TOTAL</th><th>COSTO</th><th>PRECIO ML</th><th>PRECIO FINAL</th> <th>COMISION ML</th> <th>TOT COMISIÓN</th><th>ITEM</th><th>ACOS</th><th>DESCUENTO TOT</th><th>COSTO ENVIO</th><th>TOTAL VENTA ML</th><th>UTILIDAD MERCADO LIBRE</th> </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['FECHA']}</td>";
        echo "<td>{$row['ORDEN']}</td>";
        echo "<td>{$row['ID SYSCOM']}</td>";
        echo "<td>{$row['PRECIO']}</td>";
        echo "<td>{$row['IVA']}</td>";
        echo "<td>{$row['TOTAL']}</td>";
        echo "<td>{$row['COSTO']}</td>";
        echo "<td>{$row['PRECIO ML']}</td>";
        echo "<td>{$row['PRECIOFINAL']}</td>";
        echo "<td>{$row['COMISION ML']}</td>";
        echo "<td>{$row['COMISIÓN']}</td>";
        echo "<td>{$row['ITEM']}</td>";
        echo "<td>{$row['ACOS_CAMPAÑA']}</td>";
        echo "<td>{$row['DESCUENTO TOT']}</td>";
        echo "<td>{$row['COSTO ENVÍO']}</td>";
        echo "<td>{$row['TOTAL VENTA ML']}</td>";
        echo "<td>{$row['UTILIDAD MERCADO LIBRE']}</td>";
        echo "</tr>";
    }
    echo "</table>";

} else {
    echo "No se encontraron resultados.";
}

// Cerrar la conexión
$conn->close();
?>