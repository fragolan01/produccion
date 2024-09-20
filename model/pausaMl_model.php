<?php

// Conexion a base de datos

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

class MeliModel {
    private $conn;
    private $token;

    public function __construct($conn) {
        $this->conn = $conn;
        // Obtener el token de autenticación
        $this->token = $this->getTokenMeli();
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

    // Método para pausar un producto en MercadoLibre
    public function pausarProducto($id_syscom) {
        // URL base de la API
        $url_base = "https://api.mercadolibre.com/items/";

        // Consulta para obtener id_pub_meli
        $sql = "
            SELECT 
                pvm.id_pub_meli, pvt.titulo, pvm.estado
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
            $stmt->bind_result($id_pub_meli, $titulo, $estado);
            
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

                // Cerrar el statement y liberar resultados
                $stmt->close();  // Cierra el statement después del SELECT

                // Llamar a actualizarEstado después de pausar el producto
                $this->actualizarEstado($id_syscom);

                // Registrar el log después de actualizar el estado
                $this->registrarLog($id_syscom, $id_pub_meli, "paused");


                 // Finalizar con el return
                return "Producto pausado correctamente.";

            } else {
                $stmt->close();  // Aseguramos cerrar el statement si no hay resultados
                return "No se encontró ningún registro con id_syscom = $id_syscom.";
            }

        } else {
            return "Error al preparar la consulta: " . $this->conn->error;
        
        }
        
    }
              
       // Método para actualizar el estado del producto
       private function actualizarEstado($id_syscom) {
        $sql = "UPDATE plataforma_ventas_meli SET estado = 0 WHERE id_producto = ?";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("s", $id_syscom);
            $stmt->execute();
            $stmt->close();
        }
    }

    
    private function registrarLog($id_syscom, $id_pub_meli, $estado) {
        $motivo = "Ejemplo de motivo";
        $titulo = "titulo"; // Asignar "titulo" a una variable
        $sql = "INSERT INTO plataforma_ventas_log_meli (fecha, status_meli, id_pub_meli, id_producto, titulo, motivo) VALUES (NOW(), ?, ?, ?, ?, ?)";
        
        if ($stmt = $this->conn->prepare($sql)) {
            // Pasar la variable $titulo en lugar de la cadena literal "titulo"
            $stmt->bind_param("sssss", $estado, $id_pub_meli, $id_syscom, $titulo, $motivo);
            $stmt->execute();
            $stmt->close();
        }
    }
    

}
