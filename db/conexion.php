<?php

//GENERAL
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
$laraiz=$root."/";
$vempresa="Fragolan Linking People DEVELOPMENT Engine";

require($laraiz."conexion_url.php");

$vurlh="https://".$vurl;
$vurlh_developmentfragolan="https://".$vurl;
$vemail="sistemas@fragolan.com";


//CONEXION
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

// Verifica la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

/*
//GENERAL
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
$laraiz=$root."/";
$vempresa="Fragolan Linking People DEVELOPMENT Engine";

require($laraiz."conexion_url.php");

$vurlh="https://".$vurl;
$vurlh_developmentfragolan="https://".$vurl;
$vemail="sistemas@fragolan.com";

//CONEXION
$laipdelhost="localhost";
$vnombredb = "fragcom_develop";
$vusuario = "fragcom_develop";
$vpassword = "S15t3ma5@Fr4g0l4N";

// Connecting, selecting database
$link = mysql_connect($laipdelhost, $vusuario, $vpassword) or die('Could not connect: ' . mysql_error());
mysql_select_db($vnombredb, $link) or die('Could not select database');


$diez_minutos=60*10;
$unahora=60*60;
$cincohoras=60*60*5;
$seishoras=60*60*6;
$docehoras=60*60*12;
$veinticuatrohoras=60*60*24;

$timestamp_s=time(); 
//$timestamp=$timestamp_s;
//$timestamp=$timestamp_s-$cincohoras;
$timestamp=$timestamp_s-$seishoras;
$tiemporestante=0;
*/
?>
