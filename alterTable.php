<?php

// Configuración de la base de datos
$host = 'localhost';
$dbname = 'fragcom_develop';
$username = 'fragcom_develop';
$password = 'S15t3ma5@Fr4g0l4N';


// Crear una nueva conexión PDO
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

// Establecer el modo de error de PDO para que lance excepciones
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Consulta SQL para seleccionar todos los datos de la tabla
// $sql_add_colum = "ALTER TABLE plataforma_ventas_temp ADD status_meli tinyint(1)";

// try{
//     // Preparar ADD COLUM
//     $stmt = $pdo->prepare($sql_add_colum);
//     $stmt->execute();

//     // Consulta para verificar si la columna fue agregada
//     $sql_check_column = "SHOW COLUMNS FROM plataforma_ventas_temp LIKE 'status_meli'";
//     $stmt = $pdo->prepare($sql_check_column);
//     $stmt->execute();

//     // Comprobar si la columna existe
//     if ($stmt->rowCount() > 0) {
//         echo "La columna 'status_meli' se agregó correctamente.";
//     } else {
//         echo "La columna 'status_meli' no se pudo agregar.";
//     }
// } catch (PDOException $e) {
//     echo "Error: " . $e->getMessage();

// }

// $sql_update = "UPDATE plataforma_ventas_temp SET status_meli=0";
$sql_update = "UPDATE plataforma_ventas_temp SET status_meli=NULL WHERE id_syscom IN 
(213893, 213892, 220837 )";
$stmt = $pdo->prepare($sql_update);
$stmt->execute();


$sql_describe = "DESCRIBE plataforma_ventas_temp";
$stmt_describe = $pdo->prepare($sql_describe);
$stmt_describe->execute();
$estructura = $stmt_describe->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'>";
echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Predeterminado</th><th>Extra</th></tr>";
foreach ($estructura as $columna) {
    echo "<tr>";
    echo "<td>{$columna['Field']}</td>";
    echo "<td>{$columna['Type']}</td>";
    echo "<td>{$columna['Null']}</td>";
    echo "<td>{$columna['Key']}</td>";
    echo "<td>{$columna['Default']}</td>";
    echo "<td>{$columna['Extra']}</td>";
    echo "</tr>";
}
echo "</table>";


// Cerrar la conexión
$pdo = null;

?>
