<?php

// Llamar al modelo
require_once('./model/pausaMl_model.php');
// require_once('./sendmail.php'); // Incluir el archivo de sendmail.php

class MeliController {
    private $meliModel;
    private $twig;

    public function __construct($conn, $twig) {
        $this->meliModel = new MeliModel($conn);
        $this->twig = $twig;
    }

    public function pausarProducto($id_syscom) {
        // Pausar el producto y obtener el resultado
        $resultado = $this->meliModel->pausarProducto($id_syscom);
    
        // Mostrar el resultado en una vista usando Twig
        echo $this->twig->render('pausaMl.html', [
            'mensaje' => $resultado['mensaje'],
            'log' => $resultado['log']
        ]);
    }
    

    /*
    public function pausarProducto($id_syscom) {
        // Pausar el producto y obtener el resultado
        $resultado = $this->meliModel->pausarProducto($id_syscom);

        // Enviar la notificación por correo usando la función enviarNotificacion
        // $this->enviarNotificacion($id_syscom, $resultado);

        // Mostrar el resultado en una vista usando Twig
        echo $this->twig->render('pausaMl.html', ['resultado' => $resultado]);
    }
    */

    /*
    // Función para enviar una notificación por correo
    private function enviarNotificacion($id_syscom, $mensaje) {
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
        $asunto = "Email de Actualizaciones de " . $vempresa . " (Mail ID: " . $mailid . ")";
        $mensajeCorreo = "Producto con id_syscom: $id_syscom ha sido pausado.\nMensaje: $mensaje";

        // Formato HTML y de texto
        $elhtml = nl2br($mensajeCorreo);
        $mensaje = strip_tags($mensajeCorreo);
        $from = $vemail;
        $to = $vemail;
        $replyto = $vemail;
        $boundary = uniqid();
        $content_type = 'text/html; boundary=' . $boundary;
        $lafechamail = date(DATE_RFC2822);
        $body = $elhtml;

        // Cabeceras
        $headers = array(
            'From' => $from,
            'To' => $to,
            'Subject' => $asunto,
            'Reply-To' => $replyto,
            'MIME-Version' => '1.0',
            'Content-Type' => $content_type,
            'Date' => $lafechamail
        );

        // Enviar correo usando PEAR Mail
        $smtp = Mail::factory('smtp', array(
            'host' => $vemailhost,
            'auth' => true,
            'username' => $vemailusuario,
            'password' => $vemailpassword
        ));

        $mail = $smtp->send($to, $headers, $body);

        // Manejar errores
        if (PEAR::isError($mail)) {
            echo "<br><br>Ocurrió un Error:<br><br>" . $mail->getMessage() . "<br>";
        } else {
            echo "<br><br>Mail enviado!";
        }
    }
    */
}

?>
