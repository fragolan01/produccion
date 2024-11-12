<?php

require_once 'model/reporte_model.php';

class ReporteController {

    public function mostrarReporte($twig) {
        $reporte = new Reporte();
        $datos = $reporte->obtenerDatos();

        foreach ($datos as &$dato) {
            if (isset($dato['precio_iva'])) {
                $dato['precio_iva'] = "$" . number_format($dato['precio_iva'], 2);
            }
            $dato['precio_total'] = "$" . number_format($dato['precio_total'], 2);

            $dato['costo_total_mxn'] = "$" . number_format($dato['costo_total_mxn'], 2);

            $dato['mxn_tot_venta'] = "$" . number_format($dato['mxn_tot_venta'], 2);

            $dato['utilidad_round'] = "$" . number_format($dato['utilidad_round'], 2);

        }


        echo $twig->render('reporte.html', [
            'datos' => $datos
        ]);
    }
}

?>
