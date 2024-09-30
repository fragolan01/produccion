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

    public $iva = 0.16;
    public $id_dominio = 9999;
    public $dolar = 0.0;
    private $conn;
    private $descuento = 0.04;

    public function __construct() {
        global $conn; 
        $this->conn = $conn; 
    }

    // Método para obtener el tipo de cambio más reciente
    public function obtenerTipoCambio() {
        $sql_tc = "
            SELECT fecha, normal 
            FROM plataforma_ventas_tipo_cambio 
            WHERE fecha = (SELECT MAX(fecha) FROM plataforma_ventas_tipo_cambio)
        ";
        $result_tc = $this->conn->query($sql_tc);

        // Verificar si la consulta obtuvo resultados
        if ($result_tc && $result_tc->num_rows > 0) {
            $row_tc = $result_tc->fetch_assoc();
            // Convertir el valor de tipo de cambio a flotante
            return floatval($row_tc["normal"]);
        } else {
            echo "No se encontró el tipo de cambio más reciente";
            return 1; // Establecer un valor por defecto en caso de error
        }
    }

    public function obtenerDatos() {
        // Consultar el tipo de cambio antes de realizar los cálculos
        $float_tc = $this->obtenerTipoCambio();

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
            t1.mxn_tot_venta,
            (SELECT precio FROM plataforma_ventas_temp WHERE id_syscom = t1.id_syscom AND fecha < t1.fecha ORDER BY fecha DESC LIMIT 1) AS precio_anterior,
            t1.precio - COALESCE((SELECT precio FROM plataforma_ventas_temp WHERE id_syscom = t1.id_syscom AND fecha < t1.fecha ORDER BY fecha DESC LIMIT 1), 0) 
            AS precio_difference,
            t1.status_meli
            FROM (
                SELECT
                    status,
                    id_syscom,
                    titulo,
                    stock,
                    inv_min,
                    fecha,
                    precio,
                    mxn_tot_venta,
                    orden,
                    ROW_NUMBER() OVER (PARTITION BY id_syscom ORDER BY fecha DESC) AS rn,
                    status_meli
                FROM plataforma_ventas_temp
            ) AS t1
            WHERE t1.rn = 1
            ORDER BY t1.orden
        ";

        $result = $this->conn->query($sql);


        if ($result->num_rows > 0) {
            $datos = $result->fetch_all(MYSQLI_ASSOC);

            foreach ($datos as &$fila) {
                // Precio de hoy usd
                $precio_iva = round(floatval($fila['precio_hoy'] * (1 + $this->iva)), 2, PHP_ROUND_HALF_UP);
                $fila['precio_iva'] = $precio_iva;

                // Precio hoy 
                $precio_total = round(floatval($precio_iva) + floatval($fila["precio_hoy"]), 2, PHP_ROUND_HALF_UP);
                $fila['precio_total'] = $precio_total;
                
                //Conto total mx  
                $costo_total_mxn = $precio_total * $float_tc;
                // $fila['costo_total_mxn'] = $costo_total_mxn;
                $fila['costo_total_mxn'] = round($costo_total_mxn, 2, PHP_ROUND_HALF_DOWN);

                // Total venta mx
                $mxn_tot_venta = floatval($fila['mxn_tot_venta']);
                $fila['mxn_tot_venta'] = round($mxn_tot_venta, 2, PHP_ROUND_HALF_DOWN);

                // Utilidad mxn
                $utilidad_round = round(floatval($mxn_tot_venta) - floatval($costo_total_mxn), 2);

                if($utilidad_round > 0) {
                    $fila['utilidad_round']=$utilidad_round;
                } elseif($utilidad_round < 0) {
                    $fila['utilidad_round']=$utilidad_round; 
                }

                // Consulta para obtener el estado de la tabla plataforma_ventas_meli
                $sql_estado = "
                    SELECT 
                        pvm.estado 
                    FROM 
                        plataforma_ventas_meli pvm
                    WHERE
                        pvm.id_producto = " . $fila['id_syscom'] . "
                ";

                $result_meli = $this->conn->query($sql_estado);

                // Verificar si la consulta tuvo éxito
                if ($result_meli && $result_meli->num_rows > 0) {
                    $estado_meli = $result_meli->fetch_assoc();
                    $fila['estado_meli'] = $estado_meli['estado']; // Agregar el estado a los resultados
                } else {
                    $fila['estado_meli'] = "No disponible"; // Si no se encontró el estado, asignar un valor predeterminado
                }
                

            }

            return $datos;
        } else {
            return [];
        }
    }

    public function __destruct() {
        // No cerrar la conexión aquí
    }
}

?>
