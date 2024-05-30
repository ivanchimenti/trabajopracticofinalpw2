<?php

class PartidaController
{
    private $presenter;
    private $model;

    public function __construct($presenter, $model)
    {
        $this->presenter = $presenter;
        $this->model = $model;

        if (!isset($_SESSION['preguntas_mostradas'])) {
            $_SESSION['preguntas_mostradas'] = [];
        }

        if (!isset($_SESSION['game_over'])) {
            $_SESSION['game_over'] = false;
        }
    }

    public function get()
    {
        if ($_SESSION['game_over']) {
            $this->presenter->render("view/partidaView.mustache", ["game_over" => true]);
            return;
        }

        $pregunta = $this->model->getPregunta($_SESSION['preguntas_mostradas']);

        if ($pregunta) {
            $respuestas = $this->model->getRespuestas($pregunta["id"]);
            $_SESSION['preguntas_mostradas'][] = $pregunta["id"];
            $this->presenter->render("view/partidaView.mustache", ["pregunta" => $pregunta, "respuestas" => $respuestas]);
        } else {
            $this->presenter->render("view/partidaView.mustache", ["no_more_questions" => true]);
        }
    }

    public function answer()
    {
        $respuestaId = $_POST['respuesta_id'];
        $respuesta = $this->model->getRespuesta($respuestaId);

        if ($respuesta) {
            $correcta = $respuesta['correcta'] == 1;
            if ($correcta) {
                $pregunta = $this->model->getPregunta($_SESSION['preguntas_mostradas']);
                if ($pregunta) {
                    $respuestas = $this->model->getRespuestas($pregunta["id"]);
                    $_SESSION['preguntas_mostradas'][] = $pregunta["id"];
                    $this->presenter->render("view/partidaView.mustache", ["pregunta" => $pregunta, "respuestas" => $respuestas]);
                } else {
                    $this->presenter->render("view/partidaView.mustache", ["no_more_questions" => true]);
                }
            } else {
                $_SESSION['game_over'] = true;
                $this->presenter->render("view/partidaView.mustache", ["game_over" => true]);
            }
        } else {
            $this->presenter->render("view/partidaView.mustache", ["error" => "Respuesta no válida."]);
        }
    }

    public function reset()
    {
        $_SESSION['preguntas_mostradas'] = [];
        $_SESSION['game_over'] = false;
        $this->get();
    }
}
