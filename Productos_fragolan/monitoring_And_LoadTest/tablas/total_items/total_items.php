<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar las dependencias necesarias
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/db/conexion.php'; // Conexión a la base de datos
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/token/token_meli.php'; // Token de autenticación

/**
 * Función que procesa un archivo JSON y almacena los datos en la base de datos
 * 
 * @param string $rutaArchivo Ruta del archivo JSON
 * @param mysqli $conn Conexión activa a la base de datos
 * @return void
 */
function procesarArchivoJson($rutaArchivo, $conn) {
    // Leer el contenido del archivo JSON
    $jsonContenido = file_get_contents($rutaArchivo);

    // Decodificar el JSON en un array asociativo
    $datos = json_decode($jsonContenido, true);

    // Validar si el JSON fue decodificado correctamente
    if ($datos === null) {
        die("Error al decodificar el archivo JSON.");
    }

    // Preparar la consulta SQL
    $sql = "INSERT INTO `plataforma_productos_total_items`(`seller_id`, `item_id`) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error al preparar la consulta: " . $conn->error);
    }

    // Capturar el `seller_id`
    $seller_id = $datos['seller_id'];

    // Recorrer los resultados e insertar en la base de datos
    foreach ($datos['results'] as $item_id) {
        $stmt->bind_param("ss", $seller_id, $item_id);
        if (!$stmt->execute()) {
            echo "Error al insertar el registro: " . $stmt->error . "\n";
        } else {
            echo "Registro insertado: Seller ID: $seller_id, Item ID: $item_id\n";
        }
    }

    // Cerrar el prepared statement
    $stmt->close();
}

// Llamar a la función
$rutaArchivo = "itemsMeli.json";
procesarArchivoJson($rutaArchivo, $conn);

// Cerrar la conexión a la base de datos
$conn->close();
?>
