<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar las dependencias necesarias
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/db/conexion.php'; // Conexión a la base de datos
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/helpers/api_meli_helper.php';

// Function insert metric campain
function metricCampania($conn, $datos) {
    $sql = "INSERT INTO `plataforma_productos_metric_campania` (`id_campaign`, `clicks`, `prints`, `cost`, `cpc`, `ctr`, `direct_amount`, `indirect_amount`, `total_amount`, `direct_units_quantity`, `indirect_units_quantity`, `direct_items_quantity`, `advertising_items_quantity`, `organic_units_quantity`, `organic_units_amount`, `organic_items_quantity`, `acos`, `cvr`, `roas`, `sov`) 
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";  

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "Error al preparar la consulta: " . $conn->error . "\n";
        return false;
    }

    $stmt->bind_param(
        "ssssssssssssssssssss",
        $datos['id_campaign'],
        $datos['clicks'],
        $datos['prints'],
        $datos['cost'],
        $datos['cpc'],
        $datos['ctr'],
        $datos['direct_amount'],
        $datos['indirect_amount'],
        $datos['total_amount'],
        $datos['direct_units_quantity'],
        $datos['indirect_units_quantity'],
        $datos['direct_items_quantity'],
        $datos['advertising_items_quantity'],
        $datos['organic_units_quantity'],
        $datos['organic_units_amount'],
        $datos['organic_items_quantity'],
        $datos['acos'],
        $datos['cvr'],
        $datos['roas'],
        $datos['sov']
    );

    $result = $stmt->execute();
    $stmt->close();

    return $result;
}

// Llamar a la función con el token definido
$campanias = obtenerCampaniasMeli($token);
// echo "<pre>";
// print_r($campanias);
// echo "</pre>";

if (isset($campanias['results']) && is_array($campanias['results'])) {
    foreach ($campanias['results'] as $campania) {
        // Verificar si 'metrics' existe
        if (isset($campania['metrics'])) {
            $metrics = $campania['metrics'];

            // Preparar los datos para insertar
            $datos = [
                'id_campaign' => $campania['id'], // Asumiendo que el ID de la campaña es necesario
                'clicks' => $metrics['clicks'],
                'prints' => $metrics['prints'],
                'cost' => $metrics['cost'],
                'cpc' => $metrics['cpc'],
                'ctr' => $metrics['ctr'],
                'direct_amount' => $metrics['direct_amount'],
                'indirect_amount' => $metrics['indirect_amount'],
                'total_amount' => $metrics['total_amount'],
                'direct_units_quantity' => $metrics['direct_units_quantity'],
                'indirect_units_quantity' => $metrics['indirect_units_quantity'],
                'direct_items_quantity' => $metrics['direct_items_quantity'],
                'advertising_items_quantity' => $metrics['advertising_items_quantity'],
                'organic_units_quantity' => $metrics['organic_units_quantity'],
                'organic_units_amount' => $metrics['organic_units_amount'],
                'organic_items_quantity' => $metrics['organic_items_quantity'],
                'acos' => $metrics['acos'],
                'cvr' => $metrics['cvr'],
                'roas' => $metrics['roas'],
                'sov' => $metrics['sov']
            ];

            // Insertar las métricas en la base de datos
            if (metricCampania($conn, $datos)) {
                echo "Métricas de la campaña " . $campania['id'] . " insertadas correctamente.<br>";
            } else {
                echo "Error al insertar las métricas de la campaña " . $campania['id'] . ".<br>";
            }
        } else {
            echo "No se encontraron métricas para la campaña " . $campania['id'] . ".<br>";
        }
    }
} else {
    echo "No se encontraron resultados en la respuesta de la API.<br>";
}