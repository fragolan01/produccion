<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle stock syscom</title>

    <style>
        /* Estilo para los botones */
        form {
            display: inline-block;
            margin-bottom: 10px;
        }
        input[type="submit"] {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%; /* Aumenta el ancho de la tabla */
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
            position: sticky; /* Fija el encabezado */
            top: 0; /* Asegura que el encabezado esté en la parte superior */
            z-index: 1; /* Asegura que el encabezado esté por encima del contenido */
        }

        .input-text {
            padding: 5px;
            width: 100px;
        }
    </style>

</head>
<h1><center>DETALLE PRODUCTOS SYSCOM</center></h1>
<body>
    
    <!-- <form action="menu.php" method="post">
        <input type="text" name="float_tc" class="input-text" placeholder="T.C.">
        <input type="submit" name="menu" value="inicio">
    </form> -->
    <form action="menu.php" method="post">
        <input type="submit" name="menu" value="Inicio">
    </form>
    <br>

</body>
</html>

<?php

// Realiza la conexión a la base de datos y demás configuraciones necesarias
$servername = "localhost"; // Servidor de base de datos
$username = "fragcom_develop"; // Usuario de MySQL
$password = "S15t3ma5@Fr4g0l4N"; // Contraseña de MySQL
$database = "fragcom_develop"; // base de datos

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $database);


// Dominio
$id_dominio = 9999;

// Archivo .txt
$archivo = 'lista_ids_detalles.txt';

// Abrir el archivo en modo lectura
$manejador = fopen($archivo, 'r', FILE_IGNORE_NEW_LINES);

// Fecha
date_default_timezone_set('America/Mexico_city');
$fecha = new DateTime();

// Establece el límite de tiempo a 300 segundos (5 minutos)
set_time_limit(600); 

// Definir la frecuencia de serie en segundos (2.5 minuto)
$frecuencia_serie = 120; 

// Dolar
$dolar = 0.0;

// Descuento
$descuento = 0.04;

// IVA
$iva = 0.16;

$sql_tc = "
SELECT fecha, normal 
FROM plataforma_ventas_tipo_cambio AS t1 
WHERE t1.fecha = (
    SELECT MAX(t1.fecha) 
    FROM plataforma_ventas_tipo_cambio AS t1)
";
$result = $conn->query($sql_tc);

$fechaConsulta = date("d-m-Y"); // Obtener la fecha actual en formato YYYY-MM-DD
if($result->num_rows > 0) {
    echo '<table>';
    echo '<tr><th>FECHA CONSULTA</th><th> T.C HOY '.  $fechaConsulta.  '</th> <th>ACTUALIZA T.C</th> <th>IVA</th></tr>';
    while($row = $result->fetch_assoc()) {
        echo '<tr>';
            echo '<td>' . $row["fecha"] . '</td>';
            echo '<td>' . $row["normal"] . '</td>';

            echo '</th><td>';
                echo '<center>';
                echo '<form name="todos_los_productos" acction="todos_los_productos" method="post">';
                echo '<input type="submit" name="update_tc" value="Modifica T.C. ">  ';
                echo'<input type="text" name="float_tc" class="input-text" placeholder="T.C." value="' . ($row["normal"]) . '">';
                echo '</center>';
            echo '</td>';

            $float_tc = floatval($row["normal"]);
            echo '<td>' . "16%" . '</td>';
        echo '</th></tr>';
    }
} else {
    echo "No se encontraron resultados";
}

$tc_especial = $float_tc;
$costo_total_mxn = 0.0;

// Check if the update_tc button was clicked
if (isset($_POST['update_tc'])) {
    // Update the value of $float_tc
    $tc_especial = floatval($_POST['float_tc']);
}

if ($tc_especial < $float_tc ) {
    echo '<table><tr><td style="background-color: #FF0000; color: #FFFFFF; font-black: bold; text-align: center;">El TC ES MENOR A: ' . $float_tc . '</td></tr></table>';
} elseif ($tc_especial != $float_tc) {
    echo '<table><tr><td style="background-color: #00FF00; color: #000000; font-weight: bold; text-align: center;">El TC UTILIZADO ES : ' . $tc_especial . '</td></tr></table>';
}


echo "<br><br>";
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
            mxn_tot_venta,
            orden,
            ROW_NUMBER() OVER (PARTITION BY id_syscom ORDER BY fecha DESC) AS rn
        FROM plataforma_ventas_temp
    ) AS t1
    WHERE t1.rn = 1
    ORDER BY t1.orden
";

$result_all = $conn->query($sql);

if($result_all->num_rows > 0) {
    echo '<table>';
    echo '<tr>
            <th><center>ORDEN</center></th>
            <th><center>ID_SYSCOM</center></th>
            <th><center>NOMBRE</center></th>
            <th><center>STOCK</center></th>
            <th><center>INV. MINIMO</center></th>
            <th><center>STATUS</center></th>
            <th><center>PRECIO AYER (USD)</center></th>
            <th><center>PRECIO HOY (USD)</center></th>
            <th><center>DIFERENCIA (USD)</center></th>
            <th><center>IVA (USD)</center></th>
            <th><center>TOTAL (USD)</center></th>
            <th><center>COSTO (MXN)</center></th>
            <th><center> TOT VENTA (MXN)</center></th>
            <th><center> UTILIDAD (MXN)</center></th>
        </tr>';

    while($row = $result_all->fetch_assoc()) {
        echo "<tr>";    
            echo "<td><center>" . $row['orden'] . "</td></center>";
            echo "<td><center>" . $row['id_syscom'] . "</td></center>";
            echo "<td>" . $row['titulo'] . "</td>";
            echo "<td><center>" . $row['stock'] . "</td></center>";
            echo "<td><center>" . $row['inv_min'] . "</td></center>";

            echo "<td>"; 
            if ($row['status'] == 1) {
                echo "<b><center><font color=green> ACTIVO</font></b></center>";
            } elseif ($row['status'] == 0) {
                echo "<b><center><font color=red> PAUSA</font></b></center>";
            } else {
                echo 'Desconocido'; // Si el estado no es ni 0 ni 1
            }
            echo "</td>";

            echo "<td><center>$" . $row['precio_anterior'] . "</td></center>";
            echo "<td><center>$" . $row['precio_hoy']. "</td><center>";

            echo "<td><center>";
            if($row['precio_difference'] < 0) {
                echo "<b><center> <font color=green>" ."$". $row['precio_difference'] . "</font></b><center>";
            } elseif($row['precio_difference'] > 0) {
                echo "<b><center> <font color=red>" ."+". "$".$row['precio_difference'] . "</font></b><center>";
            } else {
                echo "<b><center><font >  S/C </font></b></center>";
            }
            echo "</td><center>";

            $precio_iva = round(floatval($row['precio_hoy'] * $iva), 2, PHP_ROUND_HALF_UP);
            $precio_total = round(floatval($precio_iva) + floatval($row["precio_hoy"]), 2, PHP_ROUND_HALF_UP);
            $costo_total_mxn = $precio_total * $float_tc;
            
            // Check if the update_tc button was clicked
            if (isset($_POST['update_tc'])) {
                // Update the value of $float_tc
                $tc_especial = floatval($_POST['float_tc']);
                $costo_total_mxn = $precio_total * $tc_especial;
            }

            echo "<td><center>$". $precio_iva ."</td></center>";
            echo "<td><center>$". $precio_total."</td></center>";
            echo "<td><center>$". round($costo_total_mxn, 2, PHP_ROUND_HALF_DOWN)."</td></center>";
            echo "<td><center>$". $mxn_tot_venta = floatval($row['mxn_tot_venta'])."</td></center>";

            echo "<td><center>"; 
            $utilidad = floatval($mxn_tot_venta) - floatval($costo_total_mxn);
            $utilidad_round = round($utilidad, 2, PHP_ROUND_HALF_UP);

            if($utilidad_round > 0) {
                echo "<b><center> <font color=green>"."$" . $utilidad_round . "</font></b><center>";
            } elseif($utilidad_round < 0) {
                $utilidad_round = sprintf("$%.2f", $utilidad_round); 
                echo "<b><center> <font color=red>". $utilidad_round ."</font></b><center>";
            } else {
                echo "<b><center><font >  S/C </font></b></center>";
            }
            echo "</td></center>";
        echo "</tr>";
    }
}

?>