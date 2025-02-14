<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar las dependencias necesarias
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/db/conexion.php'; // Conexión a la base de datos

/**
 * Función para leer un archivo línea por línea y devolver un array con los valores
 * 
 * @param string $rutaArchivo Ruta del archivo a leer
 * @return array Lista de valores extraídos del archivo
 */
function leerArchivo($rutaArchivo) {
    if (!file_exists($rutaArchivo)) {
        die("El archivo $rutaArchivo no existe.\n");
    }

    $lineas = file($rutaArchivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return $lineas ?: [];
}

/**
 * Función para obtener los IDs de productos que ya están en la tabla `plataforma_productos_syscom`
 * 
 * @param mysqli $conn Conexión activa a la base de datos
 * @return array Lista de IDs de productos existentes
 */
function obtenerIdsExistentes($conn) {
    $sql = "SELECT id_producto_syscom FROM plataforma_productos_syscom";
    $result = $conn->query($sql);

    $idsExistentes = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $idsExistentes[] = $row['id_producto_syscom'];
        }
    } else {
        echo "Error al consultar la base de datos: " . $conn->error . "\n";
    }

    return $idsExistentes;
}

/**
 * Función para hacer una llamada GET a la API de SYSCOM
 * 
 * @param string $idSyscom ID del producto en SYSCOM
 * @param string $token Token de autorización
 * @return array|null Respuesta decodificada de la API o null si falla
 */
function consultarApiSyscom($idSyscom, $token) {
    $url = "https://developers.syscom.mx/api/v1/productos/$idSyscom";
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
 * Función para actualizar el campo `total_existencia` en la tabla `plataforma_productos_syscom`
 * 
 * @param mysqli $conn Conexión activa a la base de datos
 * @param string $idSyscom ID del producto en SYSCOM
 * @param int $totalExistencia Nuevo valor de `total_existencia`
 * @return bool Resultado de la operación
 */
function actualizarExistencia($conn, $idSyscom, $totalExistencia) {
    $sql = "UPDATE plataforma_productos_syscom 
            SET total_existencia = ? 
            WHERE id_producto_syscom = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "Error al preparar la consulta: " . $conn->error . "\n";
        return false;
    }

    $stmt->bind_param("ds", $totalExistencia, $idSyscom);
    $result = $stmt->execute();

    if (!$result) {
        echo "Error al actualizar el producto $idSyscom: " . $stmt->error . "\n";
    }

    $stmt->close();
    return $result;
}

/**
 * Proceso principal para obtener datos de la API y actualizar `total_existencia`
 * 
 * @param mysqli $conn Conexión activa a la base de datos
 * @param string $rutaArchivo Ruta del archivo con los IDs de productos
 * @param string $token Token de autorización para la API de SYSCOM
 */
function actualizarExistenciasSyscom($conn, $rutaArchivo, $token) {
    $idsSyscom = leerArchivo($rutaArchivo);
    $idsExistentes = obtenerIdsExistentes($conn);

    // Filtrar los IDs que ya están en la tabla
    $idsActualizar = array_intersect($idsSyscom, $idsExistentes);

    foreach ($idsActualizar as $idSyscom) {
        $response = consultarApiSyscom($idSyscom, $token);

        if ($response && isset($response['total_existencia'])) {
            $totalExistencia = $response['total_existencia'];

            if (actualizarExistencia($conn, $idSyscom, $totalExistencia)) {
                echo "Existencia actualizada correctamente para el producto: $idSyscom\n";
            } else {
                echo "Error al actualizar la existencia para el producto: $idSyscom\n";
            }
        } else {
            echo "Error al obtener datos para el producto: $idSyscom\n";
        }
    }
}

// Ruta de los archivos necesarios
$rutaArchivoIds = $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/files/prod_syscom.txt';
$rutaArchivoToken = $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/files/tokenSyscom.txt';

// Leer el token desde el archivo
$token = leerArchivo($rutaArchivoToken)[0] ?? null;

if (!$token) {
    die("El token de SYSCOM no está disponible.\n");
}

// Ejecutar el proceso de actualización
actualizarExistenciasSyscom($conn, $rutaArchivoIds, $token);

// Cerrar la conexión a la base de datos
$conn->close();
?>