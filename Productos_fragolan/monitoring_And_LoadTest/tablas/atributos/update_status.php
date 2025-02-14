<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar las dependencias necesarias
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/db/conexion.php'; // Conexión a la base de datos
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/files/token/token_meli.php'; // Clase MeliToken

// Obtener el token de autenticación
$meliToken = new MeliToken();
$token = $meliToken->getTokenMeli();


/**
 * Función para obtener los item_id desde la tabla `plataforma_productos_total_items`
 * 
 * @param mysqli $conn Conexión activa a la base de datos
 * @return array Lista de item_id
 */
function obtenerItemIds($conn) {
    $itemIds = [];
    $sql = "SELECT item_id FROM plataforma_productos_total_items";
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
 * Función para actualizar el estado del producto en la tabla `plataforma_productos_atributos`
 * 
 * @param mysqli $conn Conexión activa a la base de datos
 * @param string $itemId ID del producto
 * @param string $status Nuevo estado del producto
 * @return bool Resultado de la operación
 */
function actualizarEstadoProducto($conn, $itemId, $status) {
    $sql = "UPDATE `plataforma_productos_atributos`
            SET `status` = ?
            WHERE `item_id` = ?;";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "Error al preparar la consulta: " . $conn->error . "\n";
        return false;
    }

    $stmt->bind_param("ss", $status, $itemId);

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
            $status = $response['status'] ?? null;

            if ($status !== null && actualizarEstadoProducto($conn, $itemId, $status)) {
                echo "Estado actualizado correctamente para el item_id: $itemId\n";
            } else {
                echo "Error al actualizar el estado para el item_id: $itemId\n";
            }
        } else {
            echo "Error al obtener datos para el item_id: $itemId\n";
        }
    }
}

// Ejecutar el proceso
procesarProductos($conn, $token);

// Cerrar la conexión a la base de datos
$conn->close();
?>