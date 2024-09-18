<?php

// Conexion a base de datos
$servername = "localhost"; // Servidor de base de datos
$username = "fragcom_develop"; // Usuario de MySQL
$password = "S15t3ma5@Fr4g0l4N"; // Contraseña de MySQL
$database = "fragcom_develop"; // base de datos

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $database);


// muestra el formato internacional para la configuración regional en_US
setlocale(LC_MONETARY, 'en_US');

class Reporte {

    private $conn;

    public function __construct() {
        global $conn; // Usar la conexión global definida en conexion.php
        $this->conn = $conn; // Asignar la conexión global a la variable $conn de la clase
    }

    public function obtenerDatos() {
        $sql = "
            SELECT
                t1.orden,
                t1.fecha,
                t1.id_syscom,
                t1.titulo,
                t1.stock,
                t1.inv_min,
                t1.status,
                t1.precio AS precio_hoy,
                (SELECT precio FROM plataforma_ventas_temp WHERE id_syscom = t1.id_syscom AND fecha < t1.fecha ORDER BY fecha DESC LIMIT 1) AS precio_anterior,
                t1.precio - COALESCE((SELECT precio FROM plataforma_ventas_temp WHERE id_syscom = t1.id_syscom AND fecha < t1.fecha ORDER BY fecha DESC LIMIT 1), 0) AS precio_difference
            FROM (
                SELECT
                    status,
                    id_syscom,
                    titulo,
                    stock,
                    inv_min,
                    fecha,
                    precio,
                    orden,
                    ROW_NUMBER() OVER (PARTITION BY id_syscom ORDER BY fecha DESC) AS rn
                FROM plataforma_ventas_temp
            ) AS t1
            WHERE t1.rn = 1
            ORDER BY t1.orden
        ";

        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }

    public function __destruct() {
        // No cerrar la conexión aquí, ya que es una conexión global que podría ser usada en otras partes del script.
    }
}

?>
