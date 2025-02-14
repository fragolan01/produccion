<?php
// Alertas de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar las dependencias necesarias
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/db/conexion.php'; // Conexión a la base de datos
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/files/token/token_meli.php'; // Clase MeliToken

/**
 * Función para obtener los id_categoria desde la tabla `plataforma_productos_arbol_de_categorias`
 * 
 * @param mysqli $conn Conexión activa a la base de datos
 * @return array Lista de id_categoria
 */
function obtenerIdCategoria($conn) {
    $idCategoria = [];
    $sql = "SELECT id_categoria FROM plataforma_productos_arbol_de_categorias";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $idCategoria[] = $row['id_categoria'];
        }
    }

    return $idCategoria;
}

/**
 * Función para realizar una consulta GET a la API de Mercado Libre
 * 
 * @param string $url URL de la API a consultar
 * @return array|null Respuesta decodificada de la API o null si falla
 */
function consultaApi($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "Error al realizar la solicitud: " . curl_error($ch) . "\n";
        return null;
    }

    curl_close($ch);
    return json_decode($response, true);
}

/**
 * Función para insertar datos en la tabla `plataforma_productos_children_categories`
 * 
 * @param mysqli $conn Conexión activa a la base de datos
 * @param string $idCategoria ID de la categoría principal
 * @param array $childrenCategories Lista de categorías hijas
 * @return void
 */
function insertarChildrenCategories($conn, $idCategoria, $childrenCategories) {
    $sql = "INSERT INTO plataforma_productos_detalle_por_categoria (id_categoria, id_children_category, name_categoria) 
            VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "Error al preparar la consulta: " . $conn->error . "\n";
        return;
    }

    foreach ($childrenCategories as $child) {
        $stmt->bind_param(
            "sss",
            $idCategoria,
            $child['id'],
            $child['name']
        );

        if (!$stmt->execute()) {
            echo "Error al insertar datos: " . $stmt->error . "\n";
        }
    }

    $stmt->close();
}

/**
 * Proceso principal para consultar la API y almacenar datos en la base de datos
 * 
 * @param mysqli $conn Conexión activa a la base de datos
 * @return void
 */
function procesarCategorias($conn) {
    $idCategorias = obtenerIdCategoria($conn);

    foreach ($idCategorias as $idCategoria) {
        $url = "https://api.mercadolibre.com/categories/" . $idCategoria;
        $response = consultaApi($url);

        if ($response && isset($response['children_categories'])) {
            insertarChildrenCategories($conn, $idCategoria, $response['children_categories']);
            echo "Datos insertados para la categoría: $idCategoria\n";
        } else {
            echo "No se pudieron obtener datos para la categoría: $idCategoria\n";
        }
    }
}

// Ejecutar el proceso principal
procesarCategorias($conn);

// Cerrar la conexión a la base de datos
$conn->close();
?>
