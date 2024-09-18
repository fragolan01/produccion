<?php

require_once 'model/reporte_model.php';

class ReporteController {

    public function mostrarReporte($twig) {
        $reporte = new Reporte();
        $datos = $reporte->obtenerDatos();

        echo $twig->render('reporte.html', [
            'datos' => $datos
        ]);
    }
}

?>
