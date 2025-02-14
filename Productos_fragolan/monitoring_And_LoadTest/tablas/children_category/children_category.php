<?php
// Alertas de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar las dependencias necesarias
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/db/conexion.php';

// Obtener id_children_category
function obtenerIdChildrenCategory($conn) {
    $idChildrenCategory = [];
    $sql = "SELECT id_children_category FROM plataforma_productos_detalle_por_categoria";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $idChildrenCategory[] = $row['id_children_category'];
        }
    }

    return $idChildrenCategory;
}

// Lee el archivo JSON
function leerCategoriasYatributos($filePath) {
    $json = file_get_contents($filePath);
    if ($json === false) {
        die('Error leyendo el archivo JSON.');
    }

    $data = json_decode($json, true);
    if ($data === null) {
        die('Error decodificando el archivo JSON.');
    }

    return $data;
}

// Insertar datos en la base de datos
function insertarChildrenCategories($conn, $idChildrenCategory, $categoriesData) {
    $sql = "INSERT INTO plataforma_productos_children_category (id_children_category, id_categoria3, name_categoria3) 
            VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "Error preparando la consulta: " . $conn->error;
        return;
    }

    foreach ($categoriesData as $category) {
        if (in_array($category['id'], $idChildrenCategory)) {
            foreach ($category['children_categories'] as $child) {
                $stmt->bind_param("sss", $category['id'], $child['id'], $child['name']);
                if (!$stmt->execute()) {
                    echo "Error al insertar datos: " . $stmt->error . "\n";
                }
            }
        }
    }

    $stmt->close();
}

// Ejecutar el proceso
$idChildrenCategory = obtenerIdChildrenCategory($conn);
$filePath = $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/consultas_Meli/categoriasYatributos.json';
$categoriesData = leerCategoriasYatributos($filePath);
insertarChildrenCategories($conn, $idChildrenCategory, $categoriesData);

// Cerrar la conexión
$conn->close();
?>
