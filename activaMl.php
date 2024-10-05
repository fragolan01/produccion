<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "fragcom_develop";
$password = "S15t3ma5@Fr4g0l4N";
$database = "fragcom_develop";

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $database);

// Verifica la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once './model/activaMl_model.php';
require_once './controller/activaMl_controller.php';

require_once 'lib/Twig/Autoloader.php';
Twig_Autoloader::register();


// Configura twig
$loader = new Twig_Loader_Filesystem('./views');
$twig = new Twig_Environment($loader);

// Verificar si el parámetro id_syscom está presente
if (isset($_GET['id_syscom'])) {
    $id_syscom = $_GET['id_syscom'];

    // Crear el controlador
    $controller = new MeliController($conn, $twig);

    // Llamar al método del controlador
    $controller->activarProducto($id_syscom);
} else {
    echo "Error: 'id_syscom' no está definido.";
}

?>
  