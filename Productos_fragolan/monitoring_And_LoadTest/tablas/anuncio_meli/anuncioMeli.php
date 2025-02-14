<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar las dependencias necesarias
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/db/conexion.php'; // Conexión a la base de datos
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/files/token/token_meli.php'; // Clase MeliToken

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
    $url = "https://api.mercadolibre.com/advertising/product_ads/items/$itemId";
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
        return null;
    }

    curl_close($ch);
    return json_decode($response, true);
}

/**
 * Función para insertar datos en la tabla plataforma_productos_anuncio_meli
 * 
 * @param mysqli $conn Conexión activa a la base de datos
 * @param array $datos Datos a insertar
 * @return bool True si la inserción fue exitosa, False en caso contrario
 */
function plataforma_productos_anuncio_meli($conn, $datos) {
    $sql = "INSERT INTO `plataforma_productos_anuncio_meli`(`item_id`, `campaign_id`, `price`, `title`, `status`, `domain_id`, `date_created`, `channel`, `brand_value_id`, `brand_value_name`, `current_level`, `permalink`) 
            VALUES 
            (?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "Error al preparar la consulta: " . $conn->error . "\n";
        return false;
    }


    // // Convertir fecha al formato TIMESTAMP
    // if (!empty($datos['date_created'])) {
    //     $datos['date_created'] = str_replace("T", "", $datos['date_created']);
    //     $datos['date_created'] = str_replace("Z", "", $datos['date_created']);
    // }
    

    $stmt->bind_param(
        "ssssssssssss",
        $datos['item_id'],
        $datos['campaign_id'],
        $datos['price'],
        $datos['title'],
        $datos['status'],
        $datos['domain_id'],
        $datos['date_created'],
        $datos['channel'],
        $datos['brand_value_id'],
        $datos['brand_value_name'],
        $datos['current_level'],
        $datos['permalink']
    );

    $result = $stmt->execute();
    $stmt->close();

    return $result;
}

// Crear una instancia de la clase MeliToken
$meliToken = new MeliToken();

// Obtener el token
$token = $meliToken->getTokenMeli();

// Obtener los item_id de la base de datos
$itemIds = obtenerItemIds($conn);

// Procesar cada item_id
foreach ($itemIds as $itemId) {
    $response = consultarApiMeli($itemId, $token);

    if ($response) {
        // Convertir el campo date_created al formato TIMESTAMP
        // $response['date_created'] = str_replace('T', ' ', str_replace('Z', '', $response['date_created']));

        // Crear el array de datos para insertar
        $datos = [
            'item_id' => $response['item_id'] ?? null,
            'campaign_id' => $response['campaign_id'] ?? null,
            'price' => $response['price'] ?? null,
            'title' => $response['title'] ?? null,
            'status' => $response['status'] ?? null,
            'domain_id' => $response['domain_id'] ?? null,
            'date_created' => $response['date_created'] ?? null,
            'channel' => $response['channel'] ?? null,
            'brand_value_id' => $response['brand_value_id']?? null,
            'brand_value_name' => $response['brand_value_name']?? null,
            'current_level' => $response['current_level'] ?? null,
            'permalink' => $response['permalink'] ?? null,
        ];

        // Insertar los datos en la base de datos
        if (plataforma_productos_anuncio_meli($conn, $datos)) {
            echo "Datos insertados correctamente para el item_id: $itemId\n";
        } else {
            echo "Error al insertar los datos para el item_id: $itemId\n";
        }
    } else {
        echo "No se pudo obtener datos para el item_id: $itemId\n";
    }
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
