<?php

// require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/db/conexion.php'; // Conexión a la base de datos

class TempTableCreator {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function createTempTable() {
        // Eliminar la tabla temporal si ya existe
        $dropTableSQL = "DROP TEMPORARY TABLE IF EXISTS temp_results";
        if (!$this->conn->query($dropTableSQL)) {
            echo "Error dropping temporary table: " . $this->conn->error . "<br>";
        }

        // SQL para crear la tabla temporal
        $createTempTableSQL = "
            CREATE TEMPORARY TABLE temp_results AS
            SELECT 
                am.item_id AS ITEM, 
                rc.campaign_id AS CAMPAIGN, 
                rc.acos_target AS ACOS
            FROM
                plataforma_productos_anuncio_meli am
            JOIN
                plataforma_productos_result_campania rc ON
                rc.campaign_id = am.campaign_id
        ";

        if ($this->conn->query($createTempTableSQL) === TRUE) {
            echo "Temporary table created successfully.<br>";
        } else {
            echo "Error creating temporary table: " . $this->conn->error . "<br>";
        }
    }

    public function fetchTempTableData() {
        $query = "SELECT * FROM temp_results";
        $result = $this->conn->query($query);

        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            echo "Error fetching data: " . $this->conn->error;
            return [];
        }
    }

    public function __destruct() {
        // No es necesario cerrar la conexión aquí, ya que se cerrará automáticamente al finalizar el script
    }
}

// Uso de la clase
$tempTableCreator = new TempTableCreator($conn); // $conn es la conexión de mysqli
$tempTableCreator->createTempTable();
$data = $tempTableCreator->fetchTempTableData();

// Mostrar los datos en formato de tabla HTML
// if (!empty($data)) {
//     echo "<table border='1' cellpadding='5' cellspacing='0'>";
//     echo "<tr><th>ITEM</th><th>CAMPAIGN</th><th>ACOS</th></tr>"; // Encabezados de la tabla

//     foreach ($data as $row) {
//         echo "<tr>";
//         echo "<td>" . htmlspecialchars($row['ITEM']) . "</td>";
//         echo "<td>" . htmlspecialchars($row['CAMPAIGN']) . "</td>";
//         echo "<td>" . htmlspecialchars($row['ACOS']) . "</td>";
//         echo "</tr>";
//     }

//     echo "</table>";
// } else {
//     echo "No data found.";
// }
?>
