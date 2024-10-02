<?php

class tc_model {

    // token
    private $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjM1NmU3OTkwNmJkYjJjYWNhYTJjMWM5MjZmZGNjM2M4ZmEzNzQ4ZGY0Y2VjZWUxOGQzMWFlY2Q3MWViODJmMjFmMWY3ZDBhMGJlZDk1NzkxIn0.eyJhdWQiOiJ5ZmQwS1g4U1REYUtPZEJ0cHB2UG4wSWVFeUdiVW1CVCIsImp0aSI6IjM1NmU3OTkwNmJkYjJjYWNhYTJjMWM5MjZmZGNjM2M4ZmEzNzQ4ZGY0Y2VjZWUxOGQzMWFlY2Q3MWViODJmMjFmMWY3ZDBhMGJlZDk1NzkxIiwiaWF0IjoxNzA2NTUxMzA3LCJuYmYiOjE3MDY1NTEzMDcsImV4cCI6MTczODA4NzMwNiwic3ViIjoiIiwic2NvcGVzIjpbXX0.jhALtrRj_tkgNVj6CZxuEAnWxG6qpUMeOrXZvRbLU7B5prHrc-zPmn4lLcaEDDgfWRTXHEyQrN1nRpO8EQLuBug1kUJm-mwCkPhFMb4U6c7u_S4O0WWB4bNrRv_CQpz1Vdvic1pIJB5PDurPrzG2KbHlzfogdeYWolCKFShqPH5eehoJ0MwJ5AlL83AqpFhqzeprjB0K9eGJMx3a5jc8fYZxQm7jgh1uNk4LfaapuMos23IWczeC_1uQ3Y1XW1yuYaHXY5f9N5RA_IfBULEQ-ya8UL7Bem1ntWRegx1oIQ2M1sGz5hsdyiepI313K61rGa9khk_wI9bmwBwHxca4X_sIMT_sdJ9yOVzgXMRFfG-QlvhNWK-4xDldbo52uYwxu094cwTFZijk9NmNQq-WfPNyHEzmBrL7lSmuPVSqokggA0LjvHPnXmYCz30NxonC-zSgVp_SEBcF7rw0qo5oKe7VDj0GmPHeNV9T1n8IfFo7LaALHfyw4KAwivecMh9XY5GC_IYBLWrjAwqystUW2uiVS660t7mDqvfKonFjgjZyVuakVU4MDBXOJEzF9FVahBUc_MqXVvWbiYWDtVCnzj6rwiaXzLplEFnH4ntsCveizJmcQCF-hPRKHKprEJQFfN7E1TK3kWM0Mfei_URjiklr1J0lR6NmsSvF-q165mE";
    
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
