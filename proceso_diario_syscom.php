<?php

require_once 'db/conexion.php'; // Conexión a la base de datos
require_once 'model/pausaMl_model.php';
require_once 'controller/pausaMl_controller.php';

// alv 10-11-24
require_once 'model/activaMl_model.php';
require_once 'controller/activaMl_controller.php';

require_once 'lib/Twig/Autoloader.php';
Twig_Autoloader::register();

// Configura twig
$loader = new Twig_Loader_Filesystem('./views');
$twig = new Twig_Environment($loader);


// alv 10-11-24
// Crear el controladores pausa, activa (fuera del if)
$controller = new MeliController($conn, $twig);
$controller_activa = new MeliController_activa($conn, $twig);


// Verificar si el parámetro id_syscom está presente
if (isset($_GET['id_syscom'])) {
    $id_syscom = $_GET['id_syscom'];

    // alv 10-11-24
    // Crear el controlador
    // $controller = new MeliController($conn, $twig);
    $controller_pausa = new MeliController_pausa($conn, $twig);
    $controller_activa = new MeliController_activa($conn, $twig);


    // alv 10-11-24
    // Llamar al método del controlador
    // $controller->pausarProducto($int_producto_id);
    $controller_pausa->pausarProducto($int_producto_id);
    $controller_activa->activarProducto($int_producto_id);

} else {
    echo "Error: 'id_syscom' no está definido.";
}


// Token de autenticación
$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjM1NmU3OTkwNmJkYjJjYWNhYTJjMWM5MjZmZGNjM2M4ZmEzNzQ4ZGY0Y2VjZWUxOGQzMWFlY2Q3MWViODJmMjFmMWY3ZDBhMGJlZDk1NzkxIn0.eyJhdWQiOiJ5ZmQwS1g4U1REYUtPZEJ0cHB2UG4wSWVFeUdiVW1CVCIsImp0aSI6IjM1NmU3OTkwNmJkYjJjYWNhYTJjMWM5MjZmZGNjM2M4ZmEzNzQ4ZGY0Y2VjZWUxOGQzMWFlY2Q3MWViODJmMjFmMWY3ZDBhMGJlZDk1NzkxIiwiaWF0IjoxNzA2NTUxMzA3LCJuYmYiOjE3MDY1NTEzMDcsImV4cCI6MTczODA4NzMwNiwic3ViIjoiIiwic2NvcGVzIjpbXX0.jhALtrRj_tkgNVj6CZxuEAnWxG6qpUMeOrXZvRbLU7B5prHrc-zPmn4lLcaEDDgfWRTXHEyQrN1nRpO8EQLuBug1kUJm-mwCkPhFMb4U6c7u_S4O0WWB4bNrRv_CQpz1Vdvic1pIJB5PDurPrzG2KbHlzfogdeYWolCKFShqPH5eehoJ0MwJ5AlL83AqpFhqzeprjB0K9eGJMx3a5jc8fYZxQm7jgh1uNk4LfaapuMos23IWczeC_1uQ3Y1XW1yuYaHXY5f9N5RA_IfBULEQ-ya8UL7Bem1ntWRegx1oIQ2M1sGz5hsdyiepI313K61rGa9khk_wI9bmwBwHxca4X_sIMT_sdJ9yOVzgXMRFfG-QlvhNWK-4xDldbo52uYwxu094cwTFZijk9NmNQq-WfPNyHEzmBrL7lSmuPVSqokggA0LjvHPnXmYCz30NxonC-zSgVp_SEBcF7rw0qo5oKe7VDj0GmPHeNV9T1n8IfFo7LaALHfyw4KAwivecMh9XY5GC_IYBLWrjAwqystUW2uiVS660t7mDqvfKonFjgjZyVuakVU4MDBXOJEzF9FVahBUc_MqXVvWbiYWDtVCnzj6rwiaXzLplEFnH4ntsCveizJmcQCF-hPRKHKprEJQFfN7E1TK3kWM0Mfei_URjiklr1J0lR6NmsSvF-q165mE";

// Dominio
$id_dominio = 9999;

// Archivo .txt
$archivo = './files/lista_ids_detalle.txt';

// Abrir el archivo en modo lectura
$manejador = fopen($archivo, 'r', FILE_IGNORE_NEW_LINES);

// Fecha
date_default_timezone_set('America/Mexico_city');
$fecha = new DateTime();

// Establece el límite de tiempo a 300 segundos (5 minutos)
set_time_limit(300); 

// Definir la frecuencia de serie en segundos (2.5 minutos)
$frecuencia_serie = 120;

// Descuento
$descuento = 0.04;


// URL tipo de cambio
$tipo_de_cambio = "https://developers.syscom.mx/api/v1/tipocambio";

// Configurar opciones para la solicitud HTTP
$options = array(
    'http' => array(
        'header'  => "Authorization: Bearer $token\r\n",
        'method'  => 'GET'
    )
);

// Crear contexto de flujo
$context = stream_context_create($options);

// Realizar la consulta a la API con el token de autenticación
$response = file_get_contents($tipo_de_cambio, false, $context);

// Verificar si la consulta fue exitosa
if ($response === FALSE) {
    echo 'Error al consultar la API SYSCOM';
} else {
    $data = json_decode($response, true);
    if ($data !== null) {
        echo "TIPO DE CAMBIO: " . $data['normal'] . "<br><br>";
        $float_tc = floatval($data['normal']);

        // Insertar datos en la tabla plataforma_ventas_tipo_cambio
        $sql = "INSERT INTO plataforma_ventas_tipo_cambio (id_dominio, fecha, normal) 
                VALUES ('$id_dominio', NOW(), '$float_tc')";
        
        if ($conn->query($sql) === TRUE) {
            $conn->commit();
            echo "Tipo de cambio insertado correctamente.";
        } else {
            $conn->rollback();
            echo "Error al insertar tipo de cambio: " . $conn->error;
        }
    } else {
        die('Error al decodificar el JSON');
    }
}


// Verificar si el archivo se abrió correctamente
if ($manejador) {
    // Leer el archivo línea por línea
    while (($linea = fgets($manejador)) !== false) {
        $partes = explode("\t", $linea);
        if (count($partes) == 4) {
            $orden = substr($partes[0], 0, 5);
            $producto_id = trim($partes[1]);
            $ìnv_minimo = trim($partes[2]);
            $tot_venta_mxn = trim($partes[3]);
            $api_url = "https://developers.syscom.mx/api/v1/productos/" . $producto_id;
            $response = file_get_contents($api_url, false, stream_context_create($options));
            if ($response !== FALSE) {
                $data = json_decode($response, true);
                if ($data !== null) {
                    $int_producto_id = intval($data['producto_id']);
                    $int_stock = intval($data['total_existencia']);
                    $int_inv_minimo = intval($ìnv_minimo);

                    if ($int_stock <= $int_inv_minimo) {
                        echo 'PAUSA'.'<br>';
                        $controller->pausarProducto($int_producto_id);  // Aquí se utiliza correctamente la instancia
                    } else {
                        echo 'ACTIVO'.'<br>';
                        $controller_activa->activarProducto($int_producto_id);

                    }
                }
            }
        }
    }
    fclose($manejador);
} else {
    echo "Error: No se pudo abrir el archivo.";
}

?>
