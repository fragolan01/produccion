<?php

// Muestra todos los errores excepto los de nivel de advertencia
error_reporting(E_ALL & ~E_WARNING);
error_reporting(0);

// Mostrar los errores en el navegador
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once './model/tc_model.php';
require_once ('/opt/cpanel/ea-php54/root/usr/share/pear/Mail.php');


class tc_controller{

    public function get_tc_data(){
    
        // Instancia el modelo
        $tc_Model = new tc_model();
        // Obtener el TC del modelo
        $tc_data = $tc_Model->get_tc();

        // Devuelve los datos
        return $tc_data;
        print($tc_data);


        // Enviar la notificación por correo con los detalles del producto pausado
        // $this->enviarNotificacion($tc_data);

        // Renderizamos la vista con Twig
        // echo $this->twig->render('tc.html', ['resultado' => $tc_data]);
        
    }

    // public function enviaTc($tc_data) {

        // // Enviar la notificación por correo con los detalles del producto pausado
        // $this->enviarNotificacion($tc_data);

        // Renderizamos la vista con Twig
        // echo $this->twig->render('tc.html', ['resultado' => $tc_data]);


    // }

    
    /*
    public function enviarNotificacion($tituloInserted) {

        //----------------Estas variables de Dominio las dejamos asi por el momento de favor:--------------

        $mailid=time()+1;
        $vempresa="Fragolan Linking People";

        //----------Este email esta redireccionado a fragolan.mail@gmail.com, fragolan.sistemas@gmail.com y fragolan.soporte@gmail.com:------------------

        $vemail="actualizaciones@fragolan.com";
        $vemailhost="mail.fragolan.com";
        $vemailusuario="actualizaciones@fragolan.com";
        $vemailpassword="l3&WQR@Dh9#A";


        //----Variables del Mensaje:-------------------------------------------------------------------

        // Asunto y cuerpo del correo
        $asunto = "Tipo de cambio (Mail ID: $mailid)";
        // $asunto="Email de Actualizaciones de ".$vempresa." (Mail ID: ".$mailid.")";


        // Construir el mensaje con los detalles del log (asegúrate de que las claves del array son correctas)
        $mensaje = "
            <html>
            <body>
                <h2>Tipo de cambio</h2>
                <p><strong>TC: </strong> {$tc_data['id']}</p>
            </body>
            </html>
        ";

        //-----NO TOCAR:--------------------------------------------------------------------------------

        $elhtml=$mensaje;
        $mensaje=strip_tags($mensaje);
        $from = $vemail;
        $to = $vemail;
        $replyto = $vemail;
        $subject = $asunto;
        $boundary = uniqid();
        $content_type = 'text/html; boundary=' . $boundary;
        $lafechamail=date(DATE_RFC2822);
        $body = $elhtml;

        $headers = array ('From' => $from, 'To' => $to, 'Subject' => $subject, 'Reply-To' => $replyto, 'MIME-Version' => '1.0', 'Content-Type' => $content_type, 'Date' => $lafechamail);
        $smtp = Mail::factory('smtp', array ('host' => $vemailhost, 'auth' => true, 'username' => $vemailusuario, 'password' => $vemailpassword));
        $mail = $smtp->send($to, $headers, $body);

        if(PEAR::isError($mail)) {
            echo "<br><br>Ocurrió un Error:<br><br>".$mail->getMessage()."<br>";
        }
        else {
            echo "<br><br>Mail enviado!";
        }

    }
    */

}
