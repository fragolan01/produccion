<?php

//Llamar al modedlo
require_once('./model/pausaMl_model.php');


class MeliController {
    private $meliModel;
    private $twig;

    public function __construct($conn, $twig) {
        $this->meliModel = new MeliModel($conn);
        $this->twig = $twig;
    }

    public function pausarProducto($id_syscom) {
        $resultado = $this->meliModel->pausarProducto($id_syscom);

        // Aquí podrías enviar una notificación por correo, si lo deseas
        // $this->enviarNotificacion($id_syscom, $resultado);

        // Mostrar el resultado en una vista usando Twig
        echo $this->twig->render('pausaMl.html', ['resultado' => $resultado]);
    }

    // Función para enviar una notificación por correo
    /*
    private function enviarNotificacion($id_syscom, $mensaje) {
        $to = 'correo@ejemplo.com';
        $subject = 'Producto pausado en MercadoLibre';
        $message = "Producto con id_syscom: $id_syscom ha sido pausado.\nMensaje: $mensaje";
        mail($to, $subject, $message);
    }
    */
}

?>
