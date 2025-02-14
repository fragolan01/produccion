<?php
// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar las dependencias necesarias
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/db/conexion.php'; // Conexión a la base de datos


/**
 * Función para obtener los item_id desde la tabla `plataforma_productos_syscom_meli`
 * 
 * @param mysqli $conn Conexión activa a la base de datos
 * @return array Lista de item_id
 */
function obtenerItemIds($conn) {
    $producto_id = [];
    $sql = "SELECT sm.producto_id FROM plataforma_productos_syscom_meli sm";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $producto_id[] = $row['producto_id'];
        }
    }

    // print_r($producto_id)."<br>";
    return $producto_id;
}


/**
 * Función para hacer una llamada GET a la API de SYSCOM
 * 
 * @param string $producto_id ID del producto en SYSCOM
 * @param string $token Token de autorización
 * @return array|null Respuesta decodificada de la API o null si falla
 */
function consultarApiSyscom($producto_id, $token) {
    $url = "https://developers.syscom.mx/api/v1/productos/$producto_id";
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



// Ruta de los archivos necesarios
$rutaArchivoToken = $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/files/tokenSyscom.txt';
