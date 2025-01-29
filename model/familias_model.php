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
                    $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImRiMDQwNzI0NjA2NTVmNzczODYwNmZjNDExNjcwM2IxYTc1MmMyNDc3YTg4MzdjYTBmNWZkYjFmODlkZmVkYWNjMmMyM2Q3YzRmZmI5MTZmIn0.eyJhdWQiOiJ5ZmQwS1g4U1REYUtPZEJ0cHB2UG4wSWVFeUdiVW1CVCIsImp0aSI6ImRiMDQwNzI0NjA2NTVmNzczODYwNmZjNDExNjcwM2IxYTc1MmMyNDc3YTg4MzdjYTBmNWZkYjFmODlkZmVkYWNjMmMyM2Q3YzRmZmI5MTZmIiwiaWF0IjoxNzM4MTY3NzY3LCJuYmYiOjE3MzgxNjc3NjcsImV4cCI6MTc2OTcwMzc2Nywic3ViIjoiIiwic2NvcGVzIjpbXX0.SZIcwjCM95rniRFIJFXtVkzatb03aFraekt61Uk-WYzgQ36v1XBZt2nHb18TtPEzCJL9Qi2TzMnzAo7cOhl9RVp2audfKz-zYNVHqgN4WfCJ9XXNqrNTT_-cgXfFvY6ZEl-8HE3ixnUZWHwGK6W4anCGg9yGU2pQ09-_ZpmdbDUFO-2ZIW1tQxHXua5JjEwcPWKDQHkt_tOYZ2vk1Mb36qPXgO_5RBk8nfHSJ2IqAfvtc9MRCPdMXTyDLjual_FNDs4UIwqlNlhKU9WwguD2dve78adw41g9F6tzWYAx8XgmMzwNOOXJsvMEWNTbAS_6WifkyC5fBBkKj6q6DSgwOp0ML0FEuce34YwPHUKP6BeE7s6BnpxKRd--24NQvGReA885dI-QA0O4eKMMyIKmSgLzysSYmj6-MGzZ17sHkhv0fo51YaDguE42YDQ-SVX0T_U4KTZvrGKKvzb2iawgEHlo5l8dcrq8fO5NchksVHRFJPFmjwR4QP_Dt-IcOga_Hn4qfZkZVUDZ7yomL91Y_qXDyq8XGXdHOxPyN0RyF6m0WW_Xwu4wvSpxclhovj65pQG9k619_xsvvNj-LEXJ7J_3U-0yq5c46iAAs1p1GF_YYSCJ7KdOJ9nwkPz8CKHwriGG5nJJD5zNtyNjN3ffukg9Imhtt99VbemoR0qSaM8";                    
                    
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
