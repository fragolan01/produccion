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

    // Consulta SQL para insertar los datos
    $sql = "SELECT * FROM plataforma_ventas_meli";

    // Ejecutar la consulta de inserción
    $pdo->exec($sql);


} catch (PDOException $e) {
    // Manejar errores de la base de datos
    echo "Error al insertar los registros: " . $e->getMessage();
}

// Cerrar la conexión
$pdo = null;
?>
