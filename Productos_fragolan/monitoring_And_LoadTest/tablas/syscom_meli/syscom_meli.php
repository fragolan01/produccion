<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar conexión a la base de datos
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/db/conexion.php';

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

    $producto_ids = [];

    // $manejador = fopen($rutaArchivo, 'rb');
    $manejador = fopen($rutaArchivo, 'r',FILE_IGNORE_NEW_LINES);

    while (($linea = fgets($manejador)) !== false) {
        $partes = explode("\t", trim($linea)); // Eliminar espacios en los extremos

        // Aceptar 4 o 5 partes
        if (count($partes) >= 4) {
            $orden = trim($partes[0]);
            $producto_id = trim($partes[1]);
            $inv_minimo = trim($partes[2]);
            $publi_meli = trim($partes[3]);

            // Si hay un quinto elemento, almacenarlo
            $extra = isset($partes[4]) ? trim($partes[4]) : null;

            // Solo agregamos la segunda columna (producto_id) si no está vacío o "NULL"
            if (!empty($publi_meli) && strtolower($publi_meli) !== 'null') {
                $publi_melis[] = $publi_meli;
            }
        }
    }

    fclose($manejador);

    return array_unique($producto_ids); // Devolver solo los valores únicos
}

// Ruta del archivo
$rutaArchivo = $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/files/all_product_publi_meli1.txt';
$contenido = file_get_contents($rutaArchivo);
$contenido = preg_replace('/^\xEF\xBB\xBF/', '', $contenido); // Elimina BOM UTF-8 si existe
file_put_contents($rutaArchivo, $contenido); // Guarda el archivo sin BOM antes de leerlo



// Obtener producto_id del archivo
$producto_ids = leerArchivo($rutaArchivo);


print_r($producto_ids); // IDs extraídos del archivo
// print_r($productos_existentes); // IDs encontrados en la BD

?>
