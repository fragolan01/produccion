<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar las dependencias necesarias
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/db/conexion.php'; // Conexión a la base de datos
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/files/token/token_meli.php'; // Clase MeliToken

// Crear instancia de la clase MeliToken
$meliToken = new MeliToken();

// Obtener el token
$token = $meliToken->getTokenMeli();


/**
 * Función para obtener los item_id desde la tabla `plataforma_productos_total_items`
 * 
 * @param mysqli $conn Conexión activa a la base de datos
 * @return array Lista de item_id
 */
function obtenerItemIds($conn) {
    $itemIds = [];
    $sql = "SELECT item_id FROM plataforma_productos_total_items where item_id= 'MLM2007744843' ";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $itemIds[] = $row['item_id'];
        }
    }

    return $itemIds;
}

/**
 * Función para hacer una llamada GET a la API de Mercado Libre para un item_id dado
 * 
 * @param string $itemId ID del producto
 * @param string $token Token de autorización
 * @return array|null Respuesta decodificada de la API o null si falla
 */
function consultarApiMeli($itemId, $token) {
    $url = "https://api.mercadolibre.com/items/$itemId";
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
 * Función para insertar atributos del producto en la tabla `plataforma_productos_atributos`
 * 
 * @param mysqli $conn Conexión activa a la base de datos
 * @param array $datos Datos del producto
 * @return bool Resultado de la operación
 */
function insertarAtributosProducto($conn, $datos) {
    $sql = "UPDATE `plataforma_productos_atributos`
            SET (`status`)
            VALUES (?);";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "Error al preparar la consulta: " . $conn->error . "\n";
        return false;
    }

    $stmt->bind_param(
        "s",
        $datos['status']
    );

    $result = $stmt->execute();
    $stmt->close();

    return $result;
}

/**
 * Proceso principal para obtener datos de la API y almacenarlos en la base de datos
 * 
 * @param mysqli $conn Conexión activa a la base de datos
 * @param string $token Token de autorización para la API de Mercado Libre
 */
function procesarProductos($conn, $token) {
    $itemIds = obtenerItemIds($conn);

    foreach ($itemIds as $itemId) {
        $response = consultarApiMeli($itemId, $token);

        if ($response) {
            $datos = [
                'status' => $response['status'] ?? null
            ];

            if (insertarAtributosProducto($conn, $datos)) {
                echo "STATUS ACTUALIZADOS correctamente para el item_id: $itemId\n";
            } else {
                echo "Error al ACTUALIZADOS STATUS para el item_id: $itemId\n";
            }
        } else {
            echo "Error al obtener datos para el item_id: $itemId\n";
        }
    }
}

// Token de autenticación
// $token = "APP_USR-5829758725953784-012513-651d7e895d72acee3856a35c6baf3cc3-1204465713";


// Ejecutar el proceso
procesarProductos($conn, $token);

// Cerrar la conexión a la base de datos
$conn->close();
?>
