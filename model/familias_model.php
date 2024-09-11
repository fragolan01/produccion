<?php

class ArchivoModel {
    private $archivo;

    public function __construct($archivo) {
        $this->archivo = $archivo;
    }

    public function leerArchivo() {
        $contenido = [];
        
        // Verificar si el archivo existe
        if (file_exists($this->archivo)) {
            $manejador = fopen($this->archivo, 'r');
            if ($manejador) {
                while (($linea = fgets($manejador)) !== false) {
                    // Dividir la línea en 4 partes
                    $partes = explode("\t", $linea);

                    // Orden
                    $orden = substr($partes[0], 0, 5);
                    // ID syscom
                    $producto_id = trim($partes[1]);
                    // Inventario mínimo
                    $inv_minimo = trim($partes[2]);
                    // Total de venta mxn
                    $tot_venta_mxn = trim($partes[3]);

                    // Construir la URL de la API
                    $api_url = "https://developers.syscom.mx/api/v1/productos/" . $producto_id;

                    // Token de autenticación
                    $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjM1NmU3OTkwNmJkYjJjYWNhYTJjMWM5MjZmZGNjM2M4ZmEzNzQ4ZGY0Y2VjZWUxOGQzMWFlY2Q3MWViODJmMjFmMWY3ZDBhMGJlZDk1NzkxIn0.eyJhdWQiOiJ5ZmQwS1g4U1REYUtPZEJ0cHB2UG4wSWVFeUdiVW1CVCIsImp0aSI6IjM1NmU3OTkwNmJkYjJjYWNhYTJjMWM5MjZmZGNjM2M4ZmEzNzQ4ZGY0Y2VjZWUxOGQzMWFlY2Q3MWViODJmMjFmMWY3ZDBhMGJlZDk1NzkxIiwiaWF0IjoxNzA2NTUxMzA3LCJuYmYiOjE3MDY1NTEzMDcsImV4cCI6MTczODA4NzMwNiwic3ViIjoiIiwic2NvcGVzIjpbXX0.jhALtrRj_tkgNVj6CZxuEAnWxG6qpUMeOrXZvRbLU7B5prHrc-zPmn4lLcaEDDgfWRTXHEyQrN1nRpO8EQLuBug1kUJm-mwCkPhFMb4U6c7u_S4O0WWB4bNrRv_CQpz1Vdvic1pIJB5PDurPrzG2KbHlzfogdeYWolCKFShqPH5eehoJ0MwJ5AlL83AqpFhqzeprjB0K9eGJMx3a5jc8fYZxQm7jgh1uNk4LfaapuMos23IWczeC_1uQ3Y1XW1yuYaHXY5f9N5RA_IfBULEQ-ya8UL7Bem1ntWRegx1oIQ2M1sGz5hsdyiepI313K61rGa9khk_wI9bmwBwHxca4X_sIMT_sdJ9yOVzgXMRFfG-QlvhNWK-4xDldbo52uYwxu094cwTFZijk9NmNQq-WfPNyHEzmBrL7lSmuPVSqokggA0LjvHPnXmYCz30NxonC-zSgVp_SEBcF7rw0qo5oKe7VDj0GmPHeNV9T1n8IfFo7LaALHfyw4KAwivecMh9XY5GC_IYBLWrjAwqystUW2uiVS660t7mDqvfKonFjgjZyVuakVU4MDBXOJEzF9FVahBUc_MqXVvWbiYWDtVCnzj6rwiaXzLplEFnH4ntsCveizJmcQCF-hPRKHKprEJQFfN7E1TK3kWM0Mfei_URjiklr1J0lR6NmsSvF-q165mE";

                    // Opciones para la solicitud HTTP
                    $options = array(
                        'http' => array(
                            'header'  => "Authorization: Bearer $token\r\n",
                            'method'  => 'GET'
                        )
                    );

                    // Consultar la API
                    $response = file_get_contents($api_url, false, stream_context_create($options));

                    // Verificar si la consulta fue exitosa
                    if ($response === FALSE) {
                        echo "Error al consultar la API SYSCOM para el producto_id $producto_id<br>";
                    } else {
                        // Procesar los datos recibidos
                        $data = json_decode($response, true);
                        
                        if ($data !== null) {
                            // Verificar si 'status_meli' está definido
                            $status_meli = isset($data['status_meli']) ? $data['status_meli'] : 'No disponible';

                            // Guardar los datos en el array $contenido[]
                            $contenido[] = [
                                'orden' => intval($orden),
                                'producto_id' => intval($data['producto_id']),
                                'titulo' => $data['titulo'],
                                'total_existencia' => intval($data['total_existencia']),
                                'inv_minimo' => intval($inv_minimo),
                                'tot_venta_mxn' => $tot_venta_mxn,
                                'precio_descuento' => isset($data['precios']['precio_descuento']) ? $data['precios']['precio_descuento'] : 'No disponible',
                                'status_meli' => $status_meli
                            ];
                        } else {
                            echo "Error al decodificar el JSON para el producto_id $producto_id<br>";
                        }
                    }
                }
                fclose($manejador);
            } else {
                throw new Exception("No se pudo abrir el archivo.");
            }
            return $contenido;
        } else {
            throw new Exception("El archivo no existe.");
        }
    }
}
?>
