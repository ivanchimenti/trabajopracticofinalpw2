<?php

class PartidaController
{
    private $presenter;
    private $model;

    public function __construct($presenter, $model)
    {
        $this->presenter = $presenter;
        $this->model = $model;

        if (!isset($_SESSION['game_over'])) {
            $_SESSION['game_over'] = false;
        }
        if (!isset($_SESSION['puntuacion'])) {
            $_SESSION['puntuacion'] = 0;
        }
    }

    public function get()
    {
        $pregunta = $this->mostrarPregunta();
        $this->manageSessionPartida($pregunta["id"]);
    }

    private function mostrarPregunta()
    {
        $tiempoActual = new DateTime();
        $_SESSION['tiempoEnvio'] = $_SESSION['tiempoEnvio'] ?? $tiempoActual->getTimestamp();

        $partidaActual = $this->model->getPartidaActual($_SESSION['user']['username']);
        if ($partidaActual) {
            if ($partidaActual['id_pregunta'] != 0) {

                $preguntaActual = $this->model->getPreguntaById($partidaActual['id_pregunta']);
                if ($preguntaActual) {
                    $respuestas = $this->model->getRespuestas($preguntaActual['id']);
                    $respuestas = $this->randomizeRespuestas($respuestas);
                }

                if ($preguntaActual && $respuestas) {
                    $this->renderPartidaView($preguntaActual, $respuestas, $_SESSION['tiempoEnvio']);
                    return $preguntaActual;
                }
            }
        }

        list($pregunta, $respuestas) = $this->getPreguntaAndRespuestas($_SESSION['user']['username']);

        if (!$pregunta || !$respuestas) {
            echo "Vista: no se pudo obtener pregunta";
            return;
        }

        $this->renderPartidaView($pregunta, $respuestas, $_SESSION['tiempoEnvio']);
        return $pregunta;
    }

    private function manageSessionPartida($preguntaId)
    {
        if (!isset($_SESSION['partida'])) {
            $_SESSION['partida'] = $this->model->addPartida($_SESSION['user']['username'], $preguntaId, $_SESSION['puntuacion']);
        } else {
            $this->model->updatePartida($_SESSION['partida']['id'], $preguntaId, $_SESSION['puntuacion']);
        }
    }

    private function renderPartidaView($pregunta, $respuestas, $tiempoEnvio)
    {
        $this->presenter->render("view/player/partidaView.mustache", ["pregunta" => $pregunta, "respuestas" => $respuestas, "tiempoEnvio" => $tiempoEnvio]);
    }

    public function answer()
    {
        $tiempoActual = new DateTime();
        $timer = $tiempoActual->getTimestamp() - $_SESSION['tiempoEnvio'];

        if ($timer > 30) {
            $this->gameOver();
            $this->presenter->render("view/player/partidaView.mustache", ["game_over" => true, "out_of_time" => true, "timer" => $timer]);
            return;
        }

        $respuestaId = $_POST['respuesta_id'];
        $respuesta = $this->model->getRespuesta($respuestaId);
        $user = $_SESSION['user'];

        if ($respuesta) {
            $correcta = $respuesta['correcta'] == 1;
            $this->model->addPreguntaRespondida($respuesta['idPregunta'], $user['username'], $correcta);
            if ($correcta) {
                $_SESSION['puntuacion'] += 1;
                $this->model->updatePartida($_SESSION['partida']['id'], 0, $_SESSION['puntuacion']);
                redirect("/partida/get");
            } else {
                $this->gameOver();
                $this->presenter->render("view/player/partidaView.mustache", ["game_over" => true]);
            }
        } else {
            $this->presenter->render("view/player/partidaView.mustache", ["error" => "Respuesta no válida."]);
        }
    }

    public function reset()
    {
        $_SESSION['game_over'] = false;
        redirect("/partida/get");
    }

    public function end()
    {
        $this->gameOver();
        $this->presenter->render("view/player/partidaView.mustache", ["game_over" => true, "out_of_time" => true, "timer" => $_SESSION['tiempoEnvio']]);
    }

    private function gameOver()
    {
        $_SESSION['game_over'] = true;
        $this->model->endPartida($_SESSION['partida']['id']);
        unset($_SESSION['partida']);
        unset($_SESSION['puntuacion']);
        unset($_SESSION['tiempoEnvio']);

    }

    private function randomizeRespuestas($respuestas)
    {
        $respuestasRandom = $respuestas;
        shuffle($respuestasRandom);
        return $respuestasRandom;
    }

    private function getPreguntaAndRespuestas($username)
    {
        $pregunta = $this->model->getPregunta($username);
        if ($pregunta) {
            $respuestas = $this->model->getRespuestas($pregunta['id']);
            $respuestas = $this->randomizeRespuestas($respuestas);
        }

        return $pregunta && $respuestas ? [$pregunta, $respuestas] : null;
    }
}
