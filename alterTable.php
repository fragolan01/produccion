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
$sql_update = "UPDATE plataforma_ventas_temp SET status_meli=1 WHERE id_syscom IN 
(204626, 93452, 205624, 170087, 166480, 194937, 170112, 167894, 165067, 220698, 224964,
207706, 217337, 209383, 219865, 224263, 222349, 217338, 211334, 144592, 144593, 144594,
78223, 78224, 78225, 190733, 190734, 223770, 163430, 144597, 144598, 163032, 163033,
81070, 78229, 200688, 81071, 67463, 67464, 158976, 183407, 196541, 183408, 196543,
67467, 158305, 195489, 88103, 195490, 183409, 183410, 196546, 67468, 195488, 196559,
183411, 93158, 183413, 67207, 196341, 67206, 71055, 205630, 202463, 177225, 177226,
177227, 191494, 212075, 212085, 215988, 214042, 201221, 201217, 201218, 212569,
210325, 222680, 204497, 194827, 194839, 194840, 161220, 161221, 222978, 222979, 161222,
161223, 161228, 161227, 161226, 161225, 213099, 213123, 213895, 213894, 213431, 213435,
213893, 213892, 72413, 72414, 160268, 201075, 174515, 210382, 210383, 216369, 215449,
215436, 215341, 176064, 176065)";
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
