<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once 'lib/Twig/Autoloader.php';
Twig_Autoloader::register();

// Configurar Twig
$loader = new Twig_Loader_Filesystem('./views');
$twig = new Twig_Environment($loader);

// Incluir el controlador
require_once 'controller/familias_controller.php';

// Crear instancia del controlador familias
$familiasController = new familias_controller();

// Obtener productos de Syscom a través del controlador
$prod_syscom = $familiasController->get_prod_syscom();

// Renderizar la plantilla Twig
echo $twig->render('familias_v3.html', ['productos' => $prod_syscom]);
