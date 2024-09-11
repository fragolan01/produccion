<?php
// require_once 'db/conexion.php';
// require_once 'controller/tc_controller.php';
require_once 'lib/Twig/Autoloader.php';
Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('./views');
$twig = new Twig_Environment($loader);

echo $twig->render('index.html');
