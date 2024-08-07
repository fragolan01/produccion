<?php

// Muestra todos los errores excepto los de nivel de advertencia
error_reporting(E_ALL & ~E_WARNING);

// Mostrar los errores en el navegador
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>PRODUCTOS SYSCOM</title>
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
            background-color: #7f69a5;
        }
    </style>
</head>
<body>

<?php
// $id_dominio=9999;

// $v7="despliega";

// if (!$v7) {
// 	$v7="despliega";
// }


// if ($v7=="despliega") {


    echo '<form action="familias.php" method="post">';
    echo '<input type="submit" name="familias_btn" value="Genera Consulta">';
    echo '</form>';

    
	echo "<br><br>";
    echo '<form action="todos_los_productos.php" method="post">';
        echo '<input type="submit" name="productos_btn" value="Muestra Consulta">';
    echo '</form>';


// }


echo '<form action="detalles_stock.php" method="post">';
echo '<input type="submit" name="detalle_btn" value="Detalle Reporte Stock">';
echo '</form>';

echo "<br>";
echo "<br>";


// echo '<form action="delete.php" method="post">';
// echo '<input type="submit" name="descarga_btn" value="delete">';
// echo '</form>';


?>

</body>
</html>
