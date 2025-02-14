<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar la clase MeliToken
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/files/token/token_meli.php'; // Clase MeliToken
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/db/conexion.php'; // Conexión a la base de datos

/**
 * Función para hacer una llamada GET a la API de Mercado Libre
 * 
 * @param string $token Token de autorización
 * @return array|null Respuesta decodificada de la API o null si falla
 */
// Ingresar variables rango de fechas
function consultarApiMeli($token) {
    $url = "https://api.mercadolibre.com/advertising/advertisers/47126/product_ads/campaigns?date_from=2024-11-01&date_to=2024-12-18";
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

// Crear una instancia de la clase MeliToken
$meliToken = new MeliToken();

// Obtener el token
$token = $meliToken->getTokenMeli();

// Consultar la API de Mercado Libre
$response = consultarApiMeli($token);

// Verificar conexión a la base de datos
if (!$conn) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}

// Verificar si hay datos en la respuesta
if ($response && isset($response['results']) && is_array($response['results'])) {
    foreach ($response['results'] as $item) {
        // Capturar los datos necesarios
        $id = $item['id'];
        $name = $item['name'];
        $status = $item['status'];
        $last_updated = $item['last_updated'];
        $date_created = $item['date_created'];
        $channel = $item['channel'];
        $acos_target = $item['acos_target'];

        // Imprimir datos capturados (para depuración)
        echo "ID: $id, Nombre: $name, Estado: $status, Última Actualización: $last_updated, Fecha Creación: $date_created, Canal: $channel, ACOS Target: $acos_target\n";

        // Insertar datos en la tabla
        $stmt = $conn->prepare("INSERT INTO plataforma_productos_result_campania 
            (campaign_id, nombre_campania, status, last_updated, date_created, channel, acos_target) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssd", $id, $name, $status, $last_updated, $date_created, $channel, $acos_target);

        if ($stmt->execute()) {
            echo "Datos insertados correctamente en la tabla.\n";
        } else {
            echo "Error al insertar datos: " . $stmt->error . "\n";
        }
        $stmt->close();
    }
} else {
    echo "No se obtuvieron resultados en la API o hubo un error.\n";
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
