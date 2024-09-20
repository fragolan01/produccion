<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// require_once 'db/conexion.php'; // Conexión a la base de datos
// require_once 'model/pausaMl_model.php';
require_once 'controller/pausaMl_controller.php';

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
    $controller->pausarProducto($id_syscom);
} else {
    echo "Error: 'id_syscom' no está definido.";
}

?>
  