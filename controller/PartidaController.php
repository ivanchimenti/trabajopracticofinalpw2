<?php

class PartidaController
{
    private $presenter;
    private $model;

    public function __construct($presenter, $model)
    {
        $this->presenter = $presenter;
        $this->model = $model;

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['preguntas_mostradas'])) {
            $_SESSION['preguntas_mostradas'] = [];
        }
    }

    public function get()
    {

        $pregunta = $this->model->getPregunta($_SESSION['preguntas_mostradas']);

        if ($pregunta) {
            $respuestas = $this->model->getRespuestas($pregunta["id"]);
            $_SESSION['preguntas_mostradas'][] = $pregunta["id"];
            $this->presenter->render("view/preguntaView.mustache", ["pregunta" => $pregunta,"respuestas" => $respuestas]);
        }else {
            // Manejar el caso en que no haya más preguntas disponibles
            $this->presenter->render("view/template/noMoreQuestionsView.mustache", []);
        }

    }
}