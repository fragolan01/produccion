<?php
// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar las dependencias necesarias
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/db/conexion.php'; // Conexión a la base de datos

// Ruta al archivo que contiene el token
$rutaArchivoToken = $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/files/tokenSyscom.txt';



/**
 * Función para obtener los id_producto_syscom desde la tabla `plataforma_productos_syscom`
 * 
 * @param mysqli $conn Conexión activa a la base de datos
 * @return array Lista de id_producto_syscom
 */
// function obtenerIdProductoSyscom($conn) {
//     $id_producto_syscom = [];
//     $sql = "SELECT id_producto_syscom 
//             FROM plataforma_productos_syscom ps 
//             WHERE ps.id_producto_syscom = 231875";
//     $result = $conn->query($sql);

//     if ($result && $result->num_rows > 0) {
//         while ($row = $result->fetch_assoc()) {
//             $id_producto_syscom[] = $row['id_producto_syscom'];
//         }
//     }

//     return $id_producto_syscom;
// }


/**
 * Función para obtener los id_producto_syscom desde la tabla `plataforma_productos_syscom`
 * 
 * @param mysqli $conn Conexión activa a la base de datos
 * @return array Lista de id_producto_syscom
 */
function obtenerIdProductoSyscom($conn) {
    $id_producto_syscom = [];
    $sql = "SELECT id_producto_syscom FROM plataforma_productos_syscom";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id_producto_syscom[] = $row['id_producto_syscom'];
        }
    }

    return $id_producto_syscom;
}



/**
 * Función para hacer una llamada GET a la API de SYSCOM
 * 
 * @param string $id_producto_syscom ID del producto a consultar
 * @param string $token Token de autorización
 * @return array|null Respuesta decodificada de la API o null si falla
 */
function consultarApiSyscom($id_producto_syscom, $token) {
    $url = "https://developers.syscom.mx/api/v1/productos/$id_producto_syscom";
    $headers = ["Authorization: Bearer $token"];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "Error al realizar la solicitud: " . curl_error($ch) . "\n";
        return null;
    }

    curl_close($ch);
    return json_decode($response, true);
}

/**
 * Función para insertar datos del producto en la tabla `plataforma_productos_precios_syscom`
 * 
 * @param mysqli $conn Conexión activa a la base de datos
 * @param array $datos Datos del producto
 * @return bool Resultado de la operación
 */
function insertarPreciosSyscom($conn, $datos) {
    $sql = "INSERT INTO `plataforma_productos_precios_syscom`
        (`id_producto_syscom`, `precio1`, `precio_especial`, `precio_descuento`, `precio_lista`) 
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
        `precio1` = VALUES(`precio1`),
        `precio_especial` = VALUES(`precio_especial`),
        `precio_descuento` = VALUES(`precio_descuento`),
        `precio_lista` = VALUES(`precio_lista`)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "Error al preparar la consulta: " . $conn->error . "\n";
        return false;
    }

    $stmt->bind_param(
        "sdddd",
        $datos['id_producto_syscom'],
        $datos['precio_1'],
        $datos['precio_especial'],
        $datos['precio_descuento'],
        $datos['precio_lista']
    );

    $result = $stmt->execute();

    if (!$result) {
        echo "Error al ejecutar la consulta: " . $stmt->error . "\n";
    }

    $stmt->close();

    return $result;
}

// Leer el token desde el archivo
if (!file_exists($rutaArchivoToken)) {
    die("El archivo del token no existe en la ruta especificada.\n");
}

$token = trim(file_get_contents($rutaArchivoToken));
if (!$token) {
    die("El archivo del token está vacío o no se pudo leer correctamente.\n");
}

// Proceso principal
$id_productos = obtenerIdProductoSyscom($conn);

foreach ($id_productos as $id_producto_syscom) {
    $respuesta = consultarApiSyscom($id_producto_syscom, $token);

    if ($respuesta && isset($respuesta['precios'])) {
        $precios = $respuesta['precios'];
        $datos = [
            'id_producto_syscom' => $id_producto_syscom,
            'precio_1' => $precios['precio_1'] ?? 0,
            'precio_especial' => $precios['precio_especial'] ?? 0,
            'precio_descuento' => $precios['precio_descuento'] ?? 0,
            'precio_lista' => $precios['precio_lista'] ?? 0,
        ];

        if (insertarPreciosSyscom($conn, $datos)) {
            echo "Datos del producto $id_producto_syscom insertados correctamente.\n";
        }
    } else {
        echo "No se pudo obtener información para el producto $id_producto_syscom.\n";
    }
}

// Cerrar la conexión
$conn->close();
?>
