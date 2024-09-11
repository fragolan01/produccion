<?php

require_once 'controller/tc_controller.php';

require_once 'lib/Twig/Autoloader.php';
Twig_Autoloader::register();

// Configura twig
$loader = new Twig_Loader_Filesystem('./views');
$twig = new Twig_Environment($loader);

// Crear instancia del controlador y obtener datos del tipo de cambio
$tcController = new tc_controller();
$tc_data = $tcController->get_tc_data(); // Obtener el tipo de cambio


// echo $twig->render('tc.html', ['tc_data'=>$tc_data]);
echo $twig->render('tc.html', ['tc_data' => $tc_data]);
