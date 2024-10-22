<?php

require_once 'db/conexion.php'; // Conexión a la base de datos

// Consulta a la base de datos

$sql_meli = "ALTER TABLE plataforma_ventas_log_meli MODIFY COLUMN id INT AUTO_INCREMENT PRIMARY KEY;";




// $sql_meli = "SELECT id_producto, estado FROM plataforma_ventas_meli WHERE id_producto IN (

// 205630,
// 218051,
// 195404,
// 202463,
// 212569
// )";
$result_estadoMeli = $conn->query($sql_meli);

// Verificar si la consulta obtuvo resultados
if ($result_estadoMeli && $result_estadoMeli->num_rows > 0) {
    // Iterar sobre los resultados
    foreach ($result_estadoMeli as $row_meli) {
        print "ID Producto: " . $row_meli['id_producto'] . " - Estado: " . intval($row_meli['estado']) . "<br>";
    }
} else {
    echo "No se encontraron productos con los IDs especificados.";
    return 1; // Establecer un valor por defecto en caso de error
}

?>
