<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>



    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajax({
                url: 'your-server-endpoint', // Cambia esto por la URL del servidor que quieras consultar
                method: 'GET',
                success: function(response) {
                    // Aquí puedes actualizar el contenido de la página con la respuesta
                    // Ejemplo: $('#someElement').html(response);
                    <iframe id="report" src="detalles_stock.php"></iframe>
                    console.log(response); // Solo para depuración
                },
                error: function() {
                    console.error('Error al obtener datos');
                }
            });
        });
    </script>

</head>
<body>

    <style>
    #report {
    width: 100%;
    height: 850px; /* Ajusta la altura según sea necesario */
    border: none;
    }
    </style>

    <!-- Agrega el iframe aquí -->
    <iframe id="report" src="detalles_stock.php"></iframe>

</body>
</html>
