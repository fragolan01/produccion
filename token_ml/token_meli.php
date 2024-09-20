<?php

class MeliToken{
    private $token_file;
    private $DB;
    private $primeros_36_caracteres;

    public function __construct(){
        // $this -> token_file = "mercadoLibre/tokens.json";
        $this -> token_file = "token_ml/tokens.json";

    }


    public function getTokenMeli() {
        $token_update = $this->token_file;
        $token = fopen($token_update, "r");
        $primeros_36_caracteres = '';

        if ($token) {
            while (($linea = fgets($token)) !== false) {
                $primeros_36_caracteres = substr($linea, 17, 75);
            }
            fclose($token); // Cerrar el archivo después de leer
        }
        return $primeros_36_caracteres; // Devolver el valor

    }


}