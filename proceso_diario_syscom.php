<?php

require_once 'db/conexion.php'; // Conexión a la base de datos

require_once './model/pausaMl_model.php';
require_once './controller/pausaMl_controller.php';

// alv 10-11-24
include_once './model/activaMl_model.php';
require_once './controller/activaMl_controller.php';    

require_once 'lib/Twig/Autoloader.php';
Twig_Autoloader::register();

// Configura twig
$loader = new Twig_Loader_Filesystem('./views');
$twig = new Twig_Environment($loader);


// alv 10-11-24
// Crear el controladores pausa
$controller = new MeliController($conn, $twig);

// Crear el controladores activa
$controller_activa = new MeliController_activa($conn, $twig);

// Proceso para pausar en tablas
$controller_pausa = new MeliModel($conn, $twig);

// Proceso para activar en tablas
$controller_activa = new MeliModel_activa($conn, $twig);


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


// Configurar opciones para la solicitud HTTP
$options = array(
    'http' => array(
        'header'  => "Authorization: Bearer $token\r\n",
        'method'  => 'GET'
    )
);

// Crear contexto de flujo
$context = stream_context_create($options);


// Verificar si el archivo se abrió correctamente
if ($manejador) {
    // Leer el archivo línea por línea
    while (($linea = fgets($manejador)) !== false) {
        $partes = explode("\t", $linea);
        if (count($partes) == 4) {
            $orden = substr($partes[0], 0, 5);
            $producto_id = trim($partes[1]);
            $inv_minimo = trim($partes[2]);
            $tot_venta_mxn = trim($partes[3]);
            $api_url = "https://developers.syscom.mx/api/v1/productos/" . $producto_id;
            $response = file_get_contents($api_url, false, stream_context_create($options));
            if ($response !== FALSE) {
                $data = json_decode($response, true);

                            
                // Valida estado meli en bd
                if ($data !== null) {
                    // Conversión de valores del arreglo $data
                    $int_producto_id = intval($data['producto_id']);
                    $int_stock = intval($data['total_existencia']);
                    $int_inv_minimo = intval($inv_minimo); 
                
                    // Consulta a la base de datos para obtener el estado de Meli
                    $sql_meli = "SELECT id_producto, estado FROM plataforma_ventas_meli WHERE id_producto = ?";
                    $stmt = $conn->prepare($sql_meli);
                    $stmt->bind_param('i', $int_producto_id);
                    $stmt->execute();
                    $result_estadoMeli = $stmt->get_result();
                
                    // Verificar si la consulta obtuvo resultados
                    if ($result_estadoMeli && $result_estadoMeli->num_rows > 0) {
                        $estado_meli = $result_estadoMeli->fetch_assoc();
                        $int_status_meli = intval($estado_meli['estado']);
                    } else {
                        echo "No se encontraron productos con el ID especificado.";
                        continue; // Establecer un valor por defecto en caso de error
                    }
                
                    if ($int_stock <= $int_inv_minimo) {
                        echo "Stock es menor o igual al inventario mínimo<br>";
                        if ($int_status_meli == 0) {
                            echo "sin cambios (estado ya pausado)<br>";
                        } else if ($int_status_meli != 2) {
                            echo "Producto será pausado<br>";
                            $controller->pausarProducto($int_producto_id); //paused
                            // alv 13-11-24
                            // $controller_pausa->actualizarEstado($int_producto_id);
                            echo $int_producto_id. "<br>";
                          
                            
                        } else {
                            echo "sin cambios (estado ya 2)<br>";
                        }
                    } else {
                        echo "Stock es mayor que el inventario mínimo<br>";
                        if ($int_status_meli == 0) {
                            echo "Producto será activado<br>";
                            $controller_activa->activarProducto($int_producto_id); //actived
                            // alv 13-11-24
                            // $controller_activa->actualizarEstado($int_producto_id);
                            echo $int_producto_id. "<br>";

                        } else if ($int_status_meli == 1) {
                            echo "sin cambios (producto ya activo)<br>";
                        } else if ($int_status_meli != 2) {
                            echo "Producto será activado<br>";
                            $controller_activa->activarProducto($int_producto_id); //actided
                            // alv 13-11-24
                            // $controller_activa->actualizarEstado($int_producto_id);
                            echo $int_producto_id. "<br>";

                        } else {
                            echo "sin cambios (estado ya 2)<br>";
                        }
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