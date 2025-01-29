<?php

class tc_model {

    // token
    private $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImRiMDQwNzI0NjA2NTVmNzczODYwNmZjNDExNjcwM2IxYTc1MmMyNDc3YTg4MzdjYTBmNWZkYjFmODlkZmVkYWNjMmMyM2Q3YzRmZmI5MTZmIn0.eyJhdWQiOiJ5ZmQwS1g4U1REYUtPZEJ0cHB2UG4wSWVFeUdiVW1CVCIsImp0aSI6ImRiMDQwNzI0NjA2NTVmNzczODYwNmZjNDExNjcwM2IxYTc1MmMyNDc3YTg4MzdjYTBmNWZkYjFmODlkZmVkYWNjMmMyM2Q3YzRmZmI5MTZmIiwiaWF0IjoxNzM4MTY3NzY3LCJuYmYiOjE3MzgxNjc3NjcsImV4cCI6MTc2OTcwMzc2Nywic3ViIjoiIiwic2NvcGVzIjpbXX0.SZIcwjCM95rniRFIJFXtVkzatb03aFraekt61Uk-WYzgQ36v1XBZt2nHb18TtPEzCJL9Qi2TzMnzAo7cOhl9RVp2audfKz-zYNVHqgN4WfCJ9XXNqrNTT_-cgXfFvY6ZEl-8HE3ixnUZWHwGK6W4anCGg9yGU2pQ09-_ZpmdbDUFO-2ZIW1tQxHXua5JjEwcPWKDQHkt_tOYZ2vk1Mb36qPXgO_5RBk8nfHSJ2IqAfvtc9MRCPdMXTyDLjual_FNDs4UIwqlNlhKU9WwguD2dve78adw41g9F6tzWYAx8XgmMzwNOOXJsvMEWNTbAS_6WifkyC5fBBkKj6q6DSgwOp0ML0FEuce34YwPHUKP6BeE7s6BnpxKRd--24NQvGReA885dI-QA0O4eKMMyIKmSgLzysSYmj6-MGzZ17sHkhv0fo51YaDguE42YDQ-SVX0T_U4KTZvrGKKvzb2iawgEHlo5l8dcrq8fO5NchksVHRFJPFmjwR4QP_Dt-IcOga_Hn4qfZkZVUDZ7yomL91Y_qXDyq8XGXdHOxPyN0RyF6m0WW_Xwu4wvSpxclhovj65pQG9k619_xsvvNj-LEXJ7J_3U-0yq5c46iAAs1p1GF_YYSCJ7KdOJ9nwkPz8CKHwriGG5nJJD5zNtyNjN3ffukg9Imhtt99VbemoR0qSaM8";
    
    // url tipo de cambio
    private $url_tc = "https://developers.syscom.mx/api/v1/tipocambio";

    //constructor
    public function __construct() {
        // No es necesario redefinir token y url_tc, ya que están inicializados.
    }

    // Configurar opciones para la solicitud HTTP
    public function get_tc() {
        $options = array(
            'http' => array(
                'header'  => "Authorization: Bearer {$this->token}\r\n",
                'method'  => 'GET'
            )
        );

        // Crear contexto de flujo
        $context = stream_context_create($options);
        $response_tc = @file_get_contents($this->url_tc, false, $context); // @ suprime el warning si falla

        // Verificar si la consulta fue exitosa
        if ($response_tc === FALSE) {
            // Manejar el error si la consulta falla
            return array('error' => 'Error al consultar la API SYSCOM');
        } else {
            // Procesar los datos recibidos
            $data = json_decode($response_tc, true);

            // Verificar si la decodificación tuvo éxito
            if ($data === null) {
                die('Error al decodificar el JSON');
            }

            return $data['normal']; // Devolver el JSON decodificado
        }        
    }


    // // Método para insertar el tipo de cambio en la tabla `plataforma_ventas_tipo_cambio`
    // public function insertarTipoCambio($id_dominio, $float_tc) {
    //     global $conn;

    //     // Consulta SQL preparada para la inserción con placeholders `?`
    //     $sql = "INSERT INTO plataforma_ventas_tipo_cambio (id_dominio, fecha, normal) 
    //             VALUES (?, NOW(), ?)";

    //     // Preparar la consulta
    //     $stmt = $conn->prepare($sql);

    //     if ($stmt === false) {
    //         die('Error en la preparación de la consulta: ' . $conn->error);
    //     }

    //     // Enlazar los parámetros a los placeholders
    //     $stmt->bind_param("id", $id_dominio, $float_tc);

    //     // Ejecutar la consulta
    //     if ($stmt->execute()) {
    //         echo "Inserción exitosa";
    //     } else {
    //         echo "Error al insertar: " . $stmt->error;
    //     }

    //     // Cerrar el statement
    //     $stmt->close();
    // }
    
}

/*
// Uso del modelo para obtener el tipo de cambio y realizar la inserción
$tc_model = new tc_model();
$float_tc = $tc_model->get_tc();  // Obtener el tipo de cambio de la API

if (!isset($float_tc['error'])) {
    $id_dominio = 9999;  // ID del dominio a insertar
    $tc_model->insertarTipoCambio($id_dominio, $float_tc);  // Insertar en la base de datos
} else {
    echo $float_tc['error'];  // Mostrar error si ocurre
}
    
*/
