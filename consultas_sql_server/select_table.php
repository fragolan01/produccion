<?php

// Conexión a la base de datos
$servername = "localhost";
$username = "fragcom_develop";
$password = "S15t3ma5@Fr4g0l4N";
$database = "fragcom_develop";

$conn = new mysqli($servername, $username, $password, $database);

// Verifica la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta SQL para eliminar el registro
// $orden = 10008;
$sql = "UPDATE plataforma_ventas_meli SET estado=1";

// Ejecutar la consulta de eliminación
if ($conn->query($sql) === TRUE) {
    // Verificar si se eliminó alguna fila
    if ($conn->affected_rows > 0) {
        echo "Estado meli se actualizo correctamente.";
    } else {
        echo "No se encontró ningún Estado Meli .";
    }
} else {
    echo "Error al UPDATR el registro: " . $conn->error;
}

// Cerrar la conexión
$conn->close();

?>
