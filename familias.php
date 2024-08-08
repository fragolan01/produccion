<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boton con CSS</title>
    <style>
        /* Estilo para los botones */
        form {
            display: inline-block;
            margin-bottom: 10px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>



<?php

echo '<form action="menu.php" method="post">';
    echo '<input type="submit" name="consulta_btn" value="Inicio">';
echo '</form>';
echo '<br>';


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



// Token de autenticación
$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjM1NmU3OTkwNmJkYjJjYWNhYTJjMWM5MjZmZGNjM2M4ZmEzNzQ4ZGY0Y2VjZWUxOGQzMWFlY2Q3MWViODJmMjFmMWY3ZDBhMGJlZDk1NzkxIn0.eyJhdWQiOiJ5ZmQwS1g4U1REYUtPZEJ0cHB2UG4wSWVFeUdiVW1CVCIsImp0aSI6IjM1NmU3OTkwNmJkYjJjYWNhYTJjMWM5MjZmZGNjM2M4ZmEzNzQ4ZGY0Y2VjZWUxOGQzMWFlY2Q3MWViODJmMjFmMWY3ZDBhMGJlZDk1NzkxIiwiaWF0IjoxNzA2NTUxMzA3LCJuYmYiOjE3MDY1NTEzMDcsImV4cCI6MTczODA4NzMwNiwic3ViIjoiIiwic2NvcGVzIjpbXX0.jhALtrRj_tkgNVj6CZxuEAnWxG6qpUMeOrXZvRbLU7B5prHrc-zPmn4lLcaEDDgfWRTXHEyQrN1nRpO8EQLuBug1kUJm-mwCkPhFMb4U6c7u_S4O0WWB4bNrRv_CQpz1Vdvic1pIJB5PDurPrzG2KbHlzfogdeYWolCKFShqPH5eehoJ0MwJ5AlL83AqpFhqzeprjB0K9eGJMx3a5jc8fYZxQm7jgh1uNk4LfaapuMos23IWczeC_1uQ3Y1XW1yuYaHXY5f9N5RA_IfBULEQ-ya8UL7Bem1ntWRegx1oIQ2M1sGz5hsdyiepI313K61rGa9khk_wI9bmwBwHxca4X_sIMT_sdJ9yOVzgXMRFfG-QlvhNWK-4xDldbo52uYwxu094cwTFZijk9NmNQq-WfPNyHEzmBrL7lSmuPVSqokggA0LjvHPnXmYCz30NxonC-zSgVp_SEBcF7rw0qo5oKe7VDj0GmPHeNV9T1n8IfFo7LaALHfyw4KAwivecMh9XY5GC_IYBLWrjAwqystUW2uiVS660t7mDqvfKonFjgjZyVuakVU4MDBXOJEzF9FVahBUc_MqXVvWbiYWDtVCnzj6rwiaXzLplEFnH4ntsCveizJmcQCF-hPRKHKprEJQFfN7E1TK3kWM0Mfei_URjiklr1J0lR6NmsSvF-q165mE";

// Dominio
$id_dominio=9999;

// Archivo .txt
$archivo = 'lista_ids_detalle.txt';

// Abrir el archivo en modo lectura
$manejador = fopen($archivo, 'r',FILE_IGNORE_NEW_LINES);

// Fecha
date_default_timezone_set('America/Mexico_city');
$fecha = $fecha = new DateTime();

// Establece el límite de tiempo a 300 segundos (5 minutos)
set_time_limit(300); 

// Definir la frecuencia de serie en segundos (2.5 minuto)
$frecuencia_serie = 120; 

// Dolar
$dolar = 0.0;

//Descuento
$descuento = 0.04;

// url tipo de cambio
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
    // Manejar el error si la consulta falla
    $result = array('error' => 'Error al consultar la API SYSCOM');
} else {
    // Procesar los datos recibidos (en este ejemplo asumimos que la respuesta es en JSON)
    $data = json_decode($response, true);

    // Verificar si la decodificación tuvo éxito
    if ($data === null) {
        die('Error al decodificar el JSON');
    }

    // Acceder a los datos
    echo "TIPO DE CAMBIO: ".$data['normal']."<br><br> ";

    // Convertir a float decimal
    $float_tc = floatval($data['normal']);

    // Insertando datos en tabla plataforma_ventas_temp
    $sql = "INSERT INTO plataforma_ventas_tipo_cambio (id_dominio, fecha, normal) 
    VALUES ('$id_dominio', NOW(), '$float_tc')";

    
    if ($conn->query($sql) === TRUE) {
        // Si la interceccion fue exitosa  
        $conn->commit();
    echo "Tipo de cambio insertado correctamente.";
    } else {
        // Si falla la inserción en plataforma_ventas_precio, hacer rollback
        $conn->rollback();
        echo "Error al insertar tipo de cambio plataforma_ventas_tipo_de_cambio: " . $conn->error;
    }    

}


// Verificar si el archivo se abrió correctamente
if ($manejador) {

    // Leer el archivo línea por línea
    while (($linea = fgets($manejador)) !== false) {

        // Dividir la línea en cuatro partes usando el tabulador como delimitador
        $partes = explode("\t", $linea);
        
        // Verificar si hay 4 partes
        if (count($partes) == 4) {

            // Extraer los primeros 5 dígitos de cada número
            // Orden
            $orden = substr($partes[0], 0, 5);
            // ID syscom
            $producto_id = trim($partes[1]);
            // Inventario minimo
            $ìnv_minimo = trim($partes[2]);
            // Total de venta mxn
            $tot_venta_mxn = trim($partes[3]);
           
            // Construye la URL de la API con el producto_id actual
            $api_url = "https://developers.syscom.mx/api/v1/productos/".$producto_id;

            // Realiza la consulta a la API con el token de autenticación
            $response = file_get_contents($api_url, false, stream_context_create($options));

            // Verificar si la consulta fue exitosa
            if ($response === FALSE) {
                // Manejar el error si la consulta falla
                echo "Error al consultar la API SYSCOM para el producto_id $producto_id<br>";
            } else {
                // Procesa los datos recibidos
                $data = json_decode($response, true);
                
                // ***PRECIO
                $array = json_decode($response, true);
                $precios = $array['precios']; 

                // ***PRECIO
                if (is_array($precios) && array_key_exists('precio_descuento', $precios)) {
                    // Guarda el precio_descuento para imprimirlo al final
                    $precio_descuento = $precios['precio_descuento'];
                } else {
                    echo "No se pudo acceder al precio de descuento.<br>";
                }
        
                
                // Verifica si la decodificación tuvo éxito
                if ($data === null) {
                    echo "Error al decodificar el JSON para el producto_id $producto_id<br>";
                } else {
                    // Accede a los datos y muestra la información
                    $producto_id=$data['producto_id'];
                    $stock = ['total_existencia'];
                    $titulo =['titulo'];

                    //Converit a integer las varibales
                    $int_orden = intval($orden);
                    $int_producto_id = intval($data['producto_id']);
                    $int_stock = intval($data['total_existencia']);
                    $int_inv_minimo = intval($ìnv_minimo); 

                    // Valida producto ACTIVO o en PAUSA
                    if($int_stock <= $ìnv_minimo){
                        $status = 0;
                        echo 'PAUSA'.'<br>';
                    }else{
                        $status = 1;
                        echo 'ACTIVO'.'<br>';
                    }

                    echo "ID: ".$data['producto_id']."<br>";
                    echo "PRODUCTO: ".$data['titulo'].'<br>';
                    echo "STOCK: ".$data['total_existencia']."<br>";
                    echo 'INV. MINI: '.$ìnv_minimo.'<br>';
                    echo 'TOT VTA MXN: '. $tot_venta_mxn.'<br>';

                    // ***PRECIO
                    if (isset($precio_descuento)) {
                        echo "PRECIO: " . $precio_descuento.'<br>';
                        echo "<br>";

                    }

                    //Estatus Meli
                    echo 'ESTATUS meli '.$data['status_meli'].'<br>';
 
                    

                    // Convertir a texto Titulo
                    $data_text = $data['titulo'];

                    //Converit a float las varibales
                    $float_precio_descuento = floatval($precio_descuento);
                    $float_tot_venta_mxn = floatval($tot_venta_mxn);

                    // Calcula PRECIO con descuento
                    $precio_con_descuento = $float_precio_descuento - ($precio_descuento * $descuento);

                    // Insertando datos
                    $sql = "INSERT INTO plataforma_ventas_temp (id_dominio, id_syscom, orden, fecha, stock, precio, inv_min, status, titulo, mxn_tot_venta) 
                    VALUES ('$id_dominio', '$int_producto_id', '$int_orden', NOW(), '$int_stock','$precio_con_descuento','$int_inv_minimo', '$status', '$data_text', '$float_tot_venta_mxn')";
            
                    if ($conn->query($sql) === TRUE) {
                            // echo "\Datos insertados correctamente en la tabla.";
                        } else {
                            echo "Error al insertar datos: " . $conn->error;
                        }
                    }

                    // UPDATE ID_PUBLI MELI
                    $sql_update = "UPDATE plataforma_ventas_temp pvt
                    JOIN plataforma_ventas_meli pvm ON pvt.id_syscom = pvm.id_producto
                    SET pvt.fk_meli_id = pvm.id
                    WHERE pvt.fk_meli_id IS NULL";
                    //$result_update = $this->DB->query($sql_update);
                     
                }
            
        } else {
            // Si la línea no tiene tres partes, mostrar un mensaje de error
            echo "Error: La línea no tiene el formato esperado.\n";
        }
    }
    
    // Cerrar el archivo
    fclose($manejador);
    // Cierra la BD
    $conn->close();

} else {
    // Si no se puede abrir el archivo, mostrar un mensaje de error
    echo "Error: No se pudo abrir el archivo.\n";
}


?>
