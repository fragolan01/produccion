<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>psusaML</title>

    <!-- <link rel="stylesheet" href="assets/css/style.css"/> -->

</head>
<body>
    
</body>
</html>

<?php
include '../navbar.php';

// Muestra todos los errores excepto los de nivel de advertencia
error_reporting(E_ALL & ~E_WARNING);
error_reporting(0);

// Mostrar los errores en el navegador
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('token_meli.php');

// Instanciar la clase MeliModel
$meliModel = new MeliModel();

// Llamar a la función getTokenMeli() para obtener el valor
$primeros_36_caracteres = $meliModel->getTokenMeli();

// Token de autorización (asegúrate de tenerlo definido correctamente)
// $token = "APP_USR-5829758725953784-080212-c808a655a8e55685d7fcafdc2336591e-1204465713";
$token = $primeros_36_caracteres;

// Datos a enviar en el cuerpo de la solicitud
$data = array(
    "status" => "paused"
);

// Convertir los datos a formato JSON
$jsonData = json_encode($data);

// Opciones para la solicitud HTTP
$options = array(
    'http' => array(
        'header'  => array(
            "Authorization: Bearer $token",
            "Content-Type: application/json",
            "Content-Length: " . strlen($jsonData)
        ),
        'method'  => 'PUT',
        'content' => $jsonData
    )
);

// Crear el contexto de la solicitud
$context = stream_context_create($options);

// URL base de la API (reemplaza con tu URL real)
$url_base = "https://api.mercadolibre.com/items/";

// Incluir la conexión a la base de datos
// require_once('conexion.php');

// Realiza la conexión a la base de datos y demás configuraciones necesarias
$servername = "localhost"; // Servidor de base de datos
$username = "fragcom_develop"; // Usuario de MySQL
$password = "S15t3ma5@Fr4g0l4N"; // Contraseña de MySQL
$database = "fragcom_develop"; // base de datos

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $database);


// Verificar si id_syscom está presente en la URL
if (isset($_GET['id_syscom'])) {
    $id_syscom = $_GET['id_syscom'];

    // Consulta para obtener el id_pub_meli
    $sql = "
        SELECT 
            pvm.id_pub_meli 
        FROM 
            plataforma_ventas_meli pvm
        WHERE
            pvm.id_producto = ?
    ";

    // Preparar la declaración
    if ($stmt = $conn->prepare($sql)) {
        // Vincular el parámetro
        $stmt->bind_param("s", $id_syscom);

        // Ejecutar la declaración
        $stmt->execute();

        // Obtener el resultado
        $stmt->bind_result($id_pub_meli);

        // Iterar sobre los resultados (aunque en este caso esperamos solo uno)
        while ($stmt->fetch()) {
            // Construir la URL completa para la solicitud PUT
            $url = $url_base . $id_pub_meli;

            // Realizar la solicitud y obtener la respuesta
            $response = file_get_contents($url, false, $context);

            // Manejar la respuesta
            if ($response === FALSE) {
                die('Error al realizar la solicitud');
            }

            // Procesar la respuesta (por ejemplo, decodificar JSON)
            $responseData = json_decode($response, true);

            // Mostrar la respuesta
            echo "Respuesta de la API para id_pub_meli $id_pub_meli: ";
            // print_r($responseData);
        }

        // Liberar resultados antes de ejecutar la siguiente consulta
        $stmt->free_result();

        // Cerrar la declaración
        $stmt->close();

        // Realizar un SELECT para verificar si el registro existe
        $select_sql = "
            SELECT 
                pvt.id_syscom
            FROM 
                plataforma_ventas_temp pvt
            WHERE 
                pvt.id_syscom = ?
        ";

        if ($select_stmt = $conn->prepare($select_sql)) {
            $select_stmt->bind_param("s", $id_syscom);
            $select_stmt->execute();
            $select_stmt->store_result();

            if ($select_stmt->num_rows > 0) {
                echo "Registro encontrado. Procediendo con el UPDATE.";

                // Update estado meli
                $sql_status_ml = "
                    UPDATE
                        plataforma_ventas_meli pvm
                    SET
                        pvm.estado = 0
                    WHERE
                        pvm.id_producto = ?    
                ";

                // Preparar la declaración de actualización
                if ($update_stmt = $conn->prepare($sql_status_ml)) {
                    // Vincular el parámetro
                    $update_stmt->bind_param("s", $id_syscom);

                    // Ejecutar la actualización
                    if ($update_stmt->execute()) {
                        echo "Estado actualizado correctamente en la base de datos.";
                    } else {
                        echo "Error al actualizar el estado en la base de datos: " . $update_stmt->error;
                    }

                    // Cerrar la declaración de actualización
                    $update_stmt->close();
                } else {
                    echo "Error al preparar la consulta de actualización: " . $conn->error;
                }
            } else {
                echo "No se encontró ningún registro con id_syscom = $id_syscom.";
            }

            // Cerrar la declaración de selección
            $select_stmt->close();
        } else {
            echo "Error al preparar la consulta SELECT: " . $conn->error;
        }
    } else {
        echo "Error al preparar la consulta SELECT para id_pub_meli: " . $conn->error;
    }
} else {
    echo "Error: 'id_syscom' no está definido.";
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MercadoLibre</title>
    <script>
        function showAlertAndRedirect(message, redirectUrl) {
            alert(message);
            window.location.href = redirectUrl;
        }
    </script>
</head>
<body>
    <h1>Activar o Desactivar Publicación</h1>

    <?php
    // PHP para manejar la respuesta de la API
    if (isset($responseData)) {
        $responseJson = json_encode($responseData);
        echo "<script>
            showAlertAndRedirect('PRODUCTO PAUSADO', 'index.html');
        </script>";
    }
    ?>
</body>
</html>