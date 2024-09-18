<?php

require_once 'controller/reporte_controller.php';

require_once 'lib/Twig/Autoloader.php';
Twig_Autoloader::register();


// Configura twig
$loader = new Twig_Loader_Filesystem('./views');
$twig = new Twig_Environment($loader);

// Crear instancia del controlador
$reporteController = new ReporteController();

// Llamar al método para mostrar el reporte
$reporteController->mostrarReporte($twig);

?>
