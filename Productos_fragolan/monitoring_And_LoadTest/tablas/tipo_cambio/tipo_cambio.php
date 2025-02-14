<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar las dependencias necesarias
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/db/conexion.php'; // Conexión a la base de datos

// Función para leer archivo
function leerArchivo($rutaArchivo) {
    if (!file_exists($rutaArchivo)) {
        die("El archivo $rutaArchivo no existe.\n");
    }

    $lineas = file($rutaArchivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return $lineas ?: [];
}

/**
 * Función para hacer una llamada GET a la API de SYSCOM
 * 
 * @param string $token Token de autorización
 * @return array|null Respuesta decodificada de la API o null si falla
 */
function consultarApiSyscom($token) {
    $url = "https://developers.syscom.mx/api/v1/tipocambio";
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
    print_r($response); // Depuración
    return json_decode($response, true);
}

/**
 * Función para insertar datos del producto en la tabla `plataforma_productos_tipo_cambio`
 * 
 * @param mysqli $conn Conexión activa a la base de datos
 * @param array $datos Datos a insertar
 * @return bool Resultado de la operación
 */
function insertarTipoDeCambioSyscom($conn, $datos) {
    $sql = "INSERT INTO `plataforma_productos_tipo_cambio`
            (`normal`, `preferencial`, `un_dia`, `una_semana`, `dos_semanas`, `tres_semanas`, `un_mes`) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            `normal` = VALUES(`normal`),
            `preferencial` = VALUES(`preferencial`),
            `un_dia` = VALUES(`un_dia`),
            `una_semana` = VALUES(`una_semana`),
            `dos_semanas` = VALUES(`dos_semanas`),
            `tres_semanas` = VALUES(`tres_semanas`),
            `un_mes` = VALUES(`un_mes`)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "Error al preparar la consulta: " . $conn->error . "\n";
        return false;
    }

    $stmt->bind_param(
        "ddddddd",
        $datos['normal'],
        $datos['preferencial'],
        $datos['un_dia'],
        $datos['una_semana'],
        $datos['dos_semanas'],
        $datos['tres_semanas'],
        $datos['un_mes']
    );

    $result = $stmt->execute();

    if (!$result) {
        echo "Error al ejecutar la consulta: " . $stmt->error . "\n";
    }

    $stmt->close();

    return $result;
}

/**
 * Proceso principal para obtener datos de la API y almacenarlos en la base de datos
 * 
 * @param string $token Token de autorización para la API de SYSCOM
 * @param mysqli $conn Conexión activa a la base de datos
 */
function procesarTipoDeCambio($token, $conn) {
    // Obtener la respuesta de la API de SYSCOM
    $response = consultarApiSyscom($token);

    if ($response) {
        // Extraer los datos necesarios
        $datos = [
            'normal' => $response['normal'] ?? 0,
            'preferencial' => $response['preferencial'] ?? 0,
            'un_dia' => $response['un_dia'] ?? 0,
            'una_semana' => $response['una_semana'] ?? 0,
            'dos_semanas' => $response['dos_semanas'] ?? 0,
            'tres_semanas' => $response['tres_semanas'] ?? 0,
            'un_mes' => $response['un_mes'] ?? 0
        ];

        // Insertar los datos en la base de datos
        if (insertarTipoDeCambioSyscom($conn, $datos)) {
            echo "Tipo de cambio insertado correctamente\n";
        } else {
            echo "Error al insertar tipo de cambio\n";
        }
    } else {
        echo "Error al obtener tipo de cambio\n";
    }
}

// Ruta de los archivos necesarios
$rutaArchivoToken = $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/files/tokenSyscom.txt';

// Leer el token desde el archivo
$token = leerArchivo($rutaArchivoToken)[0] ?? null;

if (!$token) {
    die("El token de SYSCOM no está disponible.\n");
}

// Llamar a la función principal para procesar y almacenar los datos
procesarTipoDeCambio($token, $conn);

// Cerrar la conexión a la base de datos
$conn->close();
?>
