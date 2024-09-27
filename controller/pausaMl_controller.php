<?php

require_once('./model/pausaMl_model.php'); // Llamar al modelo


class MeliController {
    private $meliModel;
    private $twig;

    public function __construct($conn, $twig) {
        $this->meliModel = new MeliModel($conn);
        $this->twig = $twig;
    }

    public function pausarProducto($id_syscom) {
        $tituloInserted = $this->meliModel->pausarProducto($id_syscom);

        echo $this->twig->render('pausaMl.html', ['resultado' => $tituloInserted]);
    }

}

/*
class MeliController {
    private $meliModel;
    private $twig;

    public function __construct($conn, $twig) {
        // Inicializar el modelo y Twig
        $this->meliModel = new MeliModel($conn);  // Ajuste en el nombre del modelo
        $this->twig = $twig;
    }

    public function pausarProducto($id_syscom) {
        // Pausar el producto y obtener el resultado
        $resultado = $this->meliModel->pausarProducto($id_syscom);

        // Verificar si se obtuvo un resultado correcto
        if (!$resultado) {
            echo "Error al pausar el producto.";
            return;
        }

        // Mostrar el resultado en una vista usando Twig
        echo $this->twig->render('pausaMl.html', [
            'mensaje' => $resultado['mensaje'],
            'log' => $resultado['log']
        ]);

        // Enviar la notificación por correo con los detalles del producto pausado
        $this->enviarNotificacion($resultado['log']);
    }

    
    // Función para enviar una notificación por correo con los detalles del producto pausado
    private function enviarNotificacion($log) {
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

        // Construir el mensaje con los detalles del log
        $mensajeCorreo = "
            <html>
            <body>
                <h2>Detalles del Producto Pausado</h2>
                <p><strong>Motivo:</strong> {$log['motivo']}</p>
                <p><strong>Título:</strong> {$log['titulo']}</p>
                <p><strong>ID Publicación MercadoLibre:</strong> {$log['id_pub_meli']}</p>
                <p><strong>ID Producto Syscom:</strong> {$log['id_producto']}</p>
                <p><strong>Estado en MercadoLibre:</strong> {$log['status_meli']}</p>
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
*/