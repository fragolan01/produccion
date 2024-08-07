<?php
require_once('modelo/producto_model.php');
require_once('modelo/tc_model.php');
require_once('modelo/consulta_model.php');

class producto_controller{
    private $model_e;
    private $tc_model;
    private $consulta;


    // Constructor definido correctamente
    function __construct(){
        $this->model_e = new producto_model();
        $this->tc_model = new tcModel();
        $this-> consulta = new ConsultaModel();

    }
    
    function index(){
      

        $query =$this->model_e->get();
        $sql_tc =$this->model_e->get_tc();

        include_once('vistas/header.php');

        include_once('vistas/index.php');

        include_once('vistas/footer.php');
    }

    public function tipodecambio() {
        // Aquí puedes cargar los datos necesarios para la vista
        $tipo_de_cambio = $this->tc_model->getTipoDeCambio();
        include_once('vistas/header.php');
        require_once('vistas/tipodecambio.php');
        include_once('vistas/footer.php');
    }


    public function genera_consulta () {
        // Aquí puedes cargar los datos necesarios para la vista
        $consulta_syscom = $this->consulta->getConsultaSyscom();
        include_once('vistas/header.php');
        require_once('vistas/genera_consulta.php');
        include_once('vistas/footer.php');
    }



}