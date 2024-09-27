<?php

// Conexión a la base de datos
$servername = "localhost";
$username = "fragcom_develop";
$password = "S15t3ma5@Fr4g0l4N";
$database = "fragcom_develop";

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $database);

// Verifica la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

class MeliModel {
    private $conn;
    private $token;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->token = $this->getTokenMeli(); // Reemplaza con tu token de acceso válido
    }


    // Método para obtener el token de autenticación
    public function getTokenMeli() {
        // Incluir el archivo que contiene la clase MeliToken
        require_once('token_ml/token_meli.php');
        // Crear una instancia de la clase MeliToken
        $meliToken = new MeliToken();
        // Llamar al método getTokenMeli() y devolver el token
        return $meliToken->getTokenMeli();
    }
    

    // Función para pausar un producto en MercadoLibre
    public function pausarProducto($id_syscom) {
        // URL base de la API
        $url_base = "https://api.mercadolibre.com/items/";

        // Consulta para obtener id_pub_meli
        $sql = "
            SELECT 
                pvm.fecha AS fecha, pvm.id_pub_meli, pvt.titulo, pvm.estado
            FROM 
                plataforma_ventas_meli pvm
            JOIN
                plataforma_ventas_temp pvt ON pvm.id_producto = pvt.id_syscom
            WHERE
                pvm.id_producto = ?
            ORDER BY 
                pvm.id_pub_meli DESC
            LIMIT 1        
        ";

        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("s", $id_syscom);
            $stmt->execute();
            $stmt->bind_result($fecha, $id_pub_meli, $titulo, $estado);            
            
            if ($stmt->fetch()) {
                // Construir la URL completa para la solicitud PUT
                $url = $url_base . $id_pub_meli;

                // Datos a enviar
                $data = array("status" => "paused");
                $jsonData = json_encode($data);

                // Opciones HTTP para la solicitud PUT
                $options = array(
                    'http' => array(
                        'header'  => array(
                            "Authorization: Bearer $this->token",
                            "Content-Type: application/json",
                            "Content-Length: " . strlen($jsonData)
                        ),
                        'method'  => 'PUT',
                        'content' => $jsonData
                    )
                );

                $context = stream_context_create($options);

                // Realizar la solicitud PUT
                $response = file_get_contents($url, false, $context);

                if ($response === FALSE) {
                    $stmt->close();  // Aseguramos cerrar el statement en caso de error
                    return 'Error al realizar la solicitud';
                }

                // Cerrar el statement después del SELECT
                $stmt->close();  

                // Actualizar el estado del producto
                $this->actualizarEstado($id_syscom);

                // Registrar el log y obtener el título insertado
                $tituloInserted = $this->registrarLog($id_syscom, $id_pub_meli, "paused", $titulo, $fecha);

                // Devolver el título insertado
                return $tituloInserted;

            } else {
                $stmt->close();  // Cerrar si no hay resultados
                return "No se encontró ningún registro con id_syscom = $id_syscom.";
            }

        } else {
            return "Error al preparar la consulta: " . $this->conn->error;
        }
    }

    // Función para actualizar el estado del producto en la base de datos
    private function actualizarEstado($id_syscom) {
        $sql = "UPDATE plataforma_ventas_meli SET estado = 0 WHERE id_producto = ?";
        
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("s", $id_syscom);
            $stmt->execute();
            $stmt->close();
        } else {
            return "Error al preparar la actualización: " . $this->conn->error;
        }
    }


    // Función para registrar el log de la operación
    private function registrarLog($id_syscom, $id_pub_meli, $estado, $titulo, $fecha) {
        
        // Registrar el log de la operación
        $motivo = "FALTA DE STOCK";
        $sql = "INSERT INTO plataforma_ventas_log_meli (fecha, status_meli, id_pub_meli, id_producto, titulo, motivo) VALUES (NOW(), ?, ?, ?, ?, ?)";
        
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("sssss", $estado, $id_pub_meli, $id_syscom, $titulo, $motivo);
            $stmt->execute();
            $stmt->close();
        } else {
            return "Error al registrar el log: " . $this->conn->error;
        }
        
        // Consulta para obtener el último id insertado en la tabla plataforma_ventas_log_meli
        $sql_select = "SELECT MAX(id) AS folio FROM plataforma_ventas_log_meli WHERE id_producto = ?";
        
        if ($stmt_select = $this->conn->prepare($sql_select)) {
            $stmt_select->bind_param("s", $id_syscom);
            $stmt_select->execute();
            $stmt_select->bind_result($id); // Obtiene el último id (folio)
            $stmt_select->fetch();
            $stmt_select->close();
        } else {
            return "Error al obtener el último id: " . $this->conn->error;
        }
        
        // Devolver un array con la información, incluyendo el id recién insertado
        return [
            'id' => $id,  // El id del registro recién insertado
            'titulo' => $titulo,
            'id_pub_meli' => $id_pub_meli,
            'id_producto' => $id_syscom,
            'status_meli' => $estado,
            'motivo' => $motivo,
            'fecha' => $fecha
        ];
    }    

}
