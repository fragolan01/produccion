<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar las dependencias necesarias
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/db/conexion.php'; // Conexión a la base de datos
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/files/token/token_meli.php'; // Clase MeliToken


function obtenerProductosSyscomMeli($conn) {
$sql = "
    WITH PrecioAyer AS (
        SELECT 
            p1.id_producto_syscom, 
            ROUND(p1.precio_descuento - (p1.precio_descuento * 4 / 100), 2) AS precio_ayer
        FROM plataforma_productos_precios_syscom p1
        WHERE fecha < CURDATE() -- Solo fechas anteriores a hoy
        AND fecha = (
            SELECT MAX(p2.fecha)
            FROM plataforma_productos_precios_syscom p2
            WHERE p2.id_producto_syscom = p1.id_producto_syscom
            AND p2.fecha < CURDATE()
        )
    )
    SELECT 
        ROW_NUMBER() OVER (ORDER BY sm.orden) AS Numero,
        sm.orden AS ORDEN, 
        sm.id_prod_syscom AS 'ID SYSCOM', 
        sm.id_item_meli AS 'PUBLI MELI',
        ROUND(ps.precio_descuento - (ps.precio_descuento * 4 / 100), 2) AS 'PRECIO DESCUENTO',
        COALESCE(pa.precio_ayer, ROUND(ps.precio_descuento - (ps.precio_descuento * 4 / 100), 2)) AS 'PRECIO AYER',
        ROUND(
            (ps.precio_descuento - (ps.precio_descuento * 4 / 100)) - 
            COALESCE(pa.precio_ayer, (ps.precio_descuento - (ps.precio_descuento * 4 / 100))), 
            2
        ) AS 'DIFERENCIA',
        sys.total_existencia AS 'EXISTENCIA',  -- Se agrega el stock
        im.inv_min AS 'INV MIN'
    FROM 
        plataforma_productos_syscom_meli sm
    JOIN 
        (
            SELECT *
            FROM plataforma_productos_precios_syscom
            WHERE fecha = (
                SELECT MAX(fecha) 
                FROM plataforma_productos_precios_syscom ps2
                WHERE ps2.id_producto_syscom = plataforma_productos_precios_syscom.id_producto_syscom
            )
        ) ps
        ON ps.id_producto_syscom = sm.id_prod_syscom
    LEFT JOIN 
        PrecioAyer pa 
        ON pa.id_producto_syscom = sm.id_prod_syscom
    JOIN 
        plataforma_productos_syscom sys  -- 🔥 Se agrega el nuevo JOIN
        ON sys.id_producto_syscom = sm.id_prod_syscom
    JOIN
        plataforma_productos_inventario_mini im
        ON im.id_syscom = sys.id_producto_syscom -- Columna inventario minimo
    ORDER BY 
        sm.orden;
";

    $resultado = $conn->query($sql);

    if ($resultado === false) {
        die("Error en la consulta: " . $conn->error);
    }

    return $resultado->fetch_all(MYSQLI_ASSOC); // Devuelve los datos como un array asociativo
}

function mostrarTablaProductos($productos) {
    if (empty($productos)) {
        echo "<p>No hay datos disponibles.</p>";
        return;
    }

    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr>
            <th>PROD</th>
            <th>ORDEN</th>
            <th>ID SYSCOM</th>
            <th>PUBLI MELI</th>
            <th>PRECIO HOY</th>
            <th>PRECIO AYER</th>
            <th>DIFERENCIA</th>
            <th>STOCK</th>
            <th>INV MINI</th>
        </tr>";

    foreach ($productos as $producto) {
        echo "<tr>";
        echo "<td>{$producto['Numero']}</td>";
        echo "<td>{$producto['ORDEN']}</td>";
        echo "<td>{$producto['ID SYSCOM']}</td>";
        echo "<td>{$producto['PUBLI MELI']}</td>";
        echo "<td>{$producto['PRECIO DESCUENTO']}</td>";
        echo "<td>{$producto['PRECIO AYER']}</td>";
        echo "<td>{$producto['DIFERENCIA']}</td>";
        echo "<td>{$producto['EXISTENCIA']}</td>";
        echo "<td>{$producto['INV MIN']}</td>";

        echo "</tr>";
    }

    echo "</table>";
}

// Uso de las funciones
$productos = obtenerProductosSyscomMeli($conn);
mostrarTablaProductos($productos);

// Cerrar la conexión a la base de datos
$conn->close();
?>


