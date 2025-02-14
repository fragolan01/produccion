<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar las dependencias necesarias
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/db/conexion.php'; // Conexión a la base de datos
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/token/token_meli.php'; // Token de autenticación

/**
 * Inserta categorías desde un archivo JSON en una tabla MySQL.
 *
 * @param mysqli $conn La conexión activa a la base de datos.
 * @param string $jsonFilePath La ruta al archivo JSON.
 * @param string $tableName El nombre de la tabla donde se insertarán las categorías.
 * @return void
 */
function insertarCategoriasDesdeJson($conn, $jsonFilePath, $tableName)
{
    if (!file_exists($jsonFilePath)) {
        die("El archivo JSON no se encontró en la ruta especificada: $jsonFilePath.");
    }

    // Leer el contenido del archivo JSON
    $jsonContent = file_get_contents($jsonFilePath);

    // Decodificar el JSON a un array asociativo
    $categorias = json_decode($jsonContent, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Error al decodificar el JSON: " . json_last_error_msg());
    }

    // Consulta SQL para insertar datos
    $sql = "INSERT INTO $tableName (id_categoria, name_categoria) VALUES (?, ?)";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error al preparar la consulta: " . $conn->error);
    }

    // Vincular parámetros y ejecutar
    foreach ($categorias as $categoria) {
        $stmt->bind_param("ss", $categoria['id'], $categoria['name']); // "ss" indica que ambos son cadenas
        if ($stmt->execute()) {
            echo "Categoría insertada: " . $categoria['name'] . "<br>";
        } else {
            echo "Error al insertar categoría: " . $categoria['name'] . " - " . $stmt->error . "<br>";
        }
    }

    // Cerrar la consulta
    $stmt->close();

    echo "Proceso de inserción finalizado.";
}

// Llamar a la función con los parámetros necesarios
$archivo = 'categoriasPorSitio.json';
$tabla = 'plataforma_productos_arbol_de_categorias';
insertarCategoriasDesdeJson($conn, $archivo, $tabla);

// Cerrar la conexión
$conn->close();
