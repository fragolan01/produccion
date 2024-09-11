<?php

// Llamar al modelo 
require_once('./model/familias_model.php');

class familias_controller{
    
    public function get_prod_syscom(){

        // Instanciar el modelo
        $ArchivoModel = new ArchivoModel('./files/lista_ids_detalle.txt');

        // Obtener los elementos
        $prod_syscom = $ArchivoModel->leerArchivo();

        return $prod_syscom;
    }
}