<?php

/**
 * Función para insertar datos en la tabla `plataforma_productos_result_campania`
 * 
 * @param mysqli $conn Conexión activa a la base de datos
 * @param array $datos Datos a insertar
 * @return bool True si la inserción fue exitosa, False en caso contrario
 */
function insertarCampania($conn, $datos) {
    $sql = "INSERT INTO `plataforma_productos_anuncio_meli`
            (`campaign_id`, `nombre_campania`, `status`, `last_updated`, `date_created`, `channel`, `acos_target`) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "Error al preparar la consulta: " . $conn->error . "\n";
        return false;
    }

    // Formatear fechas
    $datos['last_updated'] = formatearFecha($datos['last_updated']);
    $datos['date_created'] = formatearFecha($datos['date_created']);

    // Vincular parámetros
    $stmt->bind_param(
        "ssssssd",
        $datos['id'],
        $datos['name'],
        $datos['status'],
        $datos['last_updated'],
        $datos['date_created'],
        $datos['channel'],
        $datos['acos_target']
    );

    $result = $stmt->execute();
    $stmt->close();

    return $result;
}

/**
 * Función para formatear fechas de formato ISO8601 a TIMESTAMP
 * 
 * @param string|null $fecha Fecha en formato ISO8601
 * @return string|null Fecha formateada o null si no existe
 */
function formatearFecha($fecha) {
    if (!empty($fecha)) {
        return str_replace(["T", "Z"], [" ", ""], $fecha);
    }
    return null;
}