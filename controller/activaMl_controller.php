<?php

//Llamar al modedlo
require_once('./model/activaMl_model.php');


class MeliController {
    private $meliModel;
    private $twig;

    public function __construct($conn, $twig) {
        $this->meliModel = new MeliModel($conn);
        $this->twig = $twig;
    }

    public function activarProducto($id_syscom) {

        // Obtenemos los detalles del producto pausado
        $tituloInserted = $this->meliModel->activarProducto($id_syscom);

        // Mostrar el resultado en una vista usando Twig
        echo $this->twig->render('activaMl.html', ['resultado' => $tituloInserted]);

        // Enviar la notificación por correo con los detalles del producto pausado
        $this->enviarNotificacion($tituloInserted);

    }

    // Función para enviar una notificación por correo con los detalles del producto pausado
    private function enviarNotificacion($tituloInserted) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        // Configuración del correo
        $mailid = time() + 1;
        $vempresa = "Fragolan Linking People";
        $vemail = "actualizaciones@fragolan.com";
        $vemailhost = "mail.fragolan.com";
        $vemailusuario = "actualizaciones@fragolan.com";
        $vemailpassword = "l3&WQR@Dh9#A";

        // Asunto y cuerpo del correo
        $asunto = "Producto pausado en MercadoLibre (Mail ID: $mailid)";

        // Construir el mensaje con los detalles del log (asegúrate de que las claves del array son correctas)
        $mensajeCorreo = "
            <html>
            <body>
                <h2>Detalles del Producto Pausado</h2>
                <p><strong>Folio: </strong> {$tituloInserted['id']}</p>
                <p><strong>Fecha: </strong> {$tituloInserted['fecha']}</p>
                <p><strong>Título: </strong> {$tituloInserted['titulo']}</p>
                <p><strong>ID Publicación: </strong> {$tituloInserted['id_pub_meli']}</p>
                <p><strong>ID Producto: </strong> {$tituloInserted['id_producto']}</p>
                <p><strong>Estado: </strong> {$tituloInserted['status_meli']}</p>
                <p><strong>Motivo: </strong> {$tituloInserted['motivo']}</p>
            </body>
            </html>
        ";

        // Configurar las cabeceras del correo
        $headers = "From: $vemail\r\n";
        $headers .= "Reply-To: $vemail\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        // Enviar correo usando mail() de PHP
        if (mail($vemail, $asunto, $mensajeCorreo, $headers)) {
            echo "<br><br>Correo enviado con éxito!";
        } else {
            echo "<br><br>Error al enviar el correo.";
        }
    }
    

}

?>