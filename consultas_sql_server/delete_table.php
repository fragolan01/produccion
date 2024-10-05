
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</head>
<body>

    <!-- Include Navbar -->
    <?php include '../navbar.php'; ?>

    <div class="../container">
        <h1><?php echo $title; ?></h1>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>


<?php
    $title = "delete tabla";


    $id_dominio=9999;
    $v7='';

if (!$v7) {
    $v7="despliega";
}

if ($v7=="despliega") {

    $servername = "localhost"; // Servidor de base de datos
    $username = "fragcom_develop"; // Usuario de MySQL
    $password = "S15t3ma5@Fr4g0l4N"; // Contraseña de MySQL
    $database = "fragcom_develop"; // base de datos
    
    // Conexión a la base de datos
    $conn = new mysqli($servername, $username, $password, $database);
    
    // Verifica la conexión
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // $sql = 'SELECT * FROM plataforma_ventas_temp WHERE id_syscom = 161220';
    // $result = $conn->query($sql);

    $sql ='ALTER TABLE plataforma_ventas_temp MODIFY mxn_tot_venta DECIMAL(10,5)';
    $result = $conn->query($sql);

    $sql = 'SHOW COLUMNS FROM plataforma_ventas_temp LIKE "mxn_tot_venta"';
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "La columna 'mxn_tot_venta' tiene el tipo: " . $row['Type'];
    } else {
        echo "No se encontró la columna 'mxn_tot_venta'.";
    }

    // Verifica si se obtuvieron resultados
    if ($result->num_rows > 0) {
        echo "<h2>Resultados de la consulta:</h2>";

        // Recorrer los resultados
        while ($row = $result->fetch_assoc()) {
            // Imprime cada fila (puedes modificar los campos según tu tabla)
            echo "ID Syscom: " . $row["id_syscom"] . " - Otro campo: " . $row["otro_campo"] . "<br>";
        }
    } else {
        echo "No se encontraron resultados para la consulta.";
    }

    echo $result;

    
}
  

// Cierra la conexión
$conn->close();


?>