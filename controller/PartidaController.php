<?php

class PartidaController
{
    private $presenter;
    private $model;

    public function __construct($presenter, $model)
    {
        $this->presenter = $presenter;
        $this->model = $model;
    }

    public function get()
    {
        $pregunta = $this->model->getPregunta();
        $respuestas = $this->model->getRespuestas($pregunta["id"]);
        $this->presenter->render("view/preguntaView.mustache", ["pregunta" => $pregunta,"respuestas" => $respuestas]);
    }
}