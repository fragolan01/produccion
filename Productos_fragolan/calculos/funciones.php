<?php
// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar las dependencias necesarias
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/db/conexion.php'; // Conexión a la base de datos

// Ruta al archivo que contiene el token
$rutaArchivoToken = $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/files/tokenSyscom.txt';

/**
 * Función para obtener los id_producto_syscom desde la tabla `plataforma_productos_syscom`
 * 
 * @param mysqli $conn Conexión activa a la base de datos
 * @return array Lista de id_producto_syscom
 */
function obtenerIdProductoSyscom($conn) {
    $id_producto_syscom = [];
    $sql = "SELECT id_producto_syscom FROM plataforma_productos_syscom";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id_producto_syscom[] = $row['id_producto_syscom'];
        }
    }

    return $id_producto_syscom;
}

/**
 * Función para obtener el descuento de cada producto `plataforma_productos_descuento`
 * 
 * @param mysqli $conn Conexión activa a la base de datos
 * @param array $id_producto_syscom Lista de IDs de productos
 * @return array Lista de descuentos
 */
function obtenerDescuentoProductoSyscom($conn, $id_producto_syscom) {
    $descuento = [];

    // Validar que $id_producto_syscom no esté vacío
    if (empty($id_producto_syscom)) {
        return $descuento;
    }

    // Crear una lista de valores para la cláusula IN
    $ids = implode(',', array_map('intval', $id_producto_syscom));
    $sql = "SELECT descuento FROM plataforma_productos_descuento WHERE id_producto_syscom IN ($ids)";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $descuento[] = $row['descuento'];
        }
    }
    // print_r($descuento);
    return $descuento;
}

// Obtener IDs de productos
$id_producto_syscom = obtenerIdProductoSyscom($conn);

// Obtener descuentos
$descuentos = obtenerDescuentoProductoSyscom($conn, $id_producto_syscom);


// Mostrar relación entre ID y descuento
if (!empty($id_producto_syscom) && !empty($descuentos)) {
    foreach ($id_producto_syscom as $index => $id) {
        $descuento = $descuentos[$index] ?? 'N/A'; // Si no hay descuento, mostrar 'N/A'
        echo "$id -> $descuento"."<br>";
    }
} else {
    echo "No hay datos disponibles para mostrar.";
}

// Cerrar la conexión
$conn->close();
?>
