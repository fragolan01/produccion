<?php
// Configuración de la base de datos
$host = 'localhost';
$dbname = 'fragcom_develop';
$username = 'fragcom_develop';
$password = 'S15t3ma5@Fr4g0l4N';

try {
    // Crear una nueva conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Establecer el modo de error de PDO para que lance excepciones
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta SQL para seleccionar todos los datos de la tabla
    $sql = "SELECT * FROM plataforma_ventas_meli";

    // Preparar y ejecutar la consulta
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Recuperar todos los registros como un arreglo asociativo
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mostrar la estructura de la tabla
    echo "<h2>Estructura de la tabla `plataforma_ventas_meli`:</h2>";
    $sql_describe = "DESCRIBE plataforma_ventas_meli";
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

    // Mostrar los datos de la tabla
    echo "<h2>Datos de la tabla `plataforma_ventas_meli`:</h2>";
    if (count($resultados) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>id</th><th>id_dominio</th><th>id_pub_meli</th><th>id_producto</th><th>estado</th><th>fecha</th></tr>";
        foreach ($resultados as $fila) {
            echo "<tr>";
            echo "<td>{$fila['id']}</td>";
            echo "<td>{$fila['id_dominio']}</td>";
            echo "<td>{$fila['id_pub_meli']}</td>";
            echo "<td>{$fila['id_producto']}</td>";
            echo "<td>{$fila['estado']}</td>";
            echo "<td>{$fila['fecha']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No hay registros en la tabla `plataforma_ventas_meli`.";
    }

} catch (PDOException $e) {
    // Manejar errores de la base de datos
    echo "Error al consultar los datos: " . $e->getMessage();
}

// Cerrar la conexión
$pdo = null;
?>
