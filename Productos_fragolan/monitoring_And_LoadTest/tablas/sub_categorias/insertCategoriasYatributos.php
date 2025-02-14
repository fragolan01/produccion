<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/db/conexion.php'; // BD

// Ruta al archivo JSON
$jsonFilePath = 'categoriasYatributos.json';

if (!file_exists($jsonFilePath)) {
    die("El archivo JSON no se encontró en la ruta especificada: $jsonFilePath.");
}

// Leer el contenido del archivo JSON
$jsonContent = file_get_contents($jsonFilePath);
$categorias = json_decode($jsonContent, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error al decodificar el JSON: " . json_last_error_msg());
}

// Iniciar una transacción
$conn->begin_transaction();

try {
    // Consulta preparada para verificar si la categoría existe
    $checkCategoriaStmt = $conn->prepare("SELECT 1 FROM plataforma_productos_categorias WHERE categoria_id = ?");
    if (!$checkCategoriaStmt) {
        throw new Exception("Error al preparar la consulta de verificación: " . $conn->error);
    }

    // Consulta preparada para insertar subcategorías
    $insertSubcategoriaStmt = $conn->prepare(
        "INSERT INTO plataforma_productos_sub_categorias (catego_id, sub_categoria_id, name_sub_categoria) VALUES (?, ?, ?)"
    );
    if (!$insertSubcategoriaStmt) {
        throw new Exception("Error al preparar la consulta de inserción: " . $conn->error);
    }

    // Recorrer el archivo JSON
    foreach ($categorias as $categoriaId => $categoriaData) {
        // Verificar si la categoría existe en la base de datos
        $checkCategoriaStmt->bind_param("s", $categoriaId);
        $checkCategoriaStmt->execute();
        $checkCategoriaStmt->store_result();

        if ($checkCategoriaStmt->num_rows > 0) {
            // La categoría existe, insertar subcategorías
            if (isset($categoriaData['children_categories']) && is_array($categoriaData['children_categories'])) {
                foreach ($categoriaData['children_categories'] as $subCategoria) {
                    $subCategoriaId = $subCategoria['id'];
                    $subCategoriaName = $subCategoria['name'];

                    $insertSubcategoriaStmt->bind_param("sss", $categoriaId, $subCategoriaId, $subCategoriaName);
                    $insertSubcategoriaStmt->execute();
                }
            }
        }
    }

    // Confirmar la transacción
    $conn->commit();

    echo "Subcategorías insertadas exitosamente.";
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollback();
    echo "Error: " . $e->getMessage();
} finally {
    // Cerrar las declaraciones preparadas
    $checkCategoriaStmt->close();
    $insertSubcategoriaStmt->close();
    $conn->close();
}
?>
