<?php
require_once './model/tc_model.php';

class tc_controller{

    public function get_tc_data(){
    
        // Instancia el modelo
        $tc_Model = new tc_model();
        // Obtener el TC del modelo
        $tc_data = $tc_Model->get_tc();
        // Devuelve los datos
        return $tc_data;

        // print($tc_data);


    }
}
