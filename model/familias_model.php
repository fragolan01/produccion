<?php

// Conexion a base de datos
// require_once('bd/conexion.php');


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


class ArchivoModel {
    private $archivo;

    public function __construct($archivo) {
        $this->archivo = $archivo;
    }

    public function leerArchivo() {

        // Dominio
        $id_dominio = 9999;

        global $conn; // Accede a la conexión global

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
                    $api_url = "https://developers.syscom.mx/api/v1/productos/".$producto_id;

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
                                'tot_venta_mxn' => intval($tot_venta_mxn),
                                'precio_descuento' => isset($data['precios']['precio_descuento']) ? $data['precios']['precio_descuento'] : 'No disponible',
                                'status_meli' => $status_meli
                            ];
                        }

                        // Consulta SQL preparada para la inserción con placeholders `?`
                        $sql = "INSERT INTO plataforma_ventas_temp (id_dominio, id_syscom, orden, fecha, stock, precio, inv_min, status, titulo, mxn_tot_venta)
                                VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?)";

                        // Preparar la consulta
                        $stmt = $conn->prepare($sql);

                        // Verificar si la preparación fue exitosa
                        if ($stmt === false) {
                            die('Error al preparar la consulta: ' . $conn->error);
                        }

                        // Insertar cada registro del arreglo
                        foreach ($contenido as $fila) {
                            // Ejecutar la consulta con los parámetros actuales usando `bind_param`
                            $stmt->bind_param(
                                "iisississ", // Tipos de los datos (int, string, etc.)
                                $id_dominio,
                                $fila['producto_id'],
                                $fila['orden'],
                                $fila['total_existencia'],
                                $fila['precio_descuento'],
                                $fila['inv_minimo'],
                                $fila['status_meli'],
                                $fila['titulo'],
                                $fila['tot_venta_mxn']
                            );

                            // Ejecutar la consulta
                            $stmt->execute();

                            // Verificar si hubo un error durante la ejecución
                            if ($stmt->error) {
                                echo "Error al insertar datos: " . $stmt->error;
                            } else {
                                echo "Datos insertados correctamente en la tabla.";
                            }
                        }

                        // Cerrar la declaración
                        $stmt->close();
                    }
                }

                fclose($manejador);
            } else {
                throw new Exception("No se pudo abrir el archivo.");
            }
        } else {
            throw new Exception("El archivo no existe.");
        }

        return $contenido;
    }
}

?>
