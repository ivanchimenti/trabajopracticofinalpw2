<?php

class GraficosController{
    private $presenter;
    private $model;
    private $graficosCreator;

    public function __construct($presenter, $model,$graficosCreator)
    {
        $this->presenter = $presenter;
        $this->model = $model;
        $this->graficosCreator = $graficosCreator;
    }

    public function create(){
        $resultado = $datay1 = array(20, 15, 23, 15);

        $html = $this->graficosCreator->create($resultado);
    }
}
