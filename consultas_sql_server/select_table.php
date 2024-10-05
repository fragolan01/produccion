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
$orden = 10008;
$sql = "DELETE FROM plataforma_ventas_temp WHERE orden = $orden";

// Ejecutar la consulta de eliminación
if ($conn->query($sql) === TRUE) {
    // Verificar si se eliminó alguna fila
    if ($conn->affected_rows > 0) {
        echo "El registro con la orden $orden se eliminó correctamente.";
    } else {
        echo "No se encontró ningún registro con la orden $orden para eliminar.";
    }
} else {
    echo "Error al eliminar el registro: " . $conn->error;
}

// Cerrar la conexión
$conn->close();

?>
