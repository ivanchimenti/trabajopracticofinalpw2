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
//        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reload']) && $_POST['reload'] == 'true') {
//            return;
//        }

        $pregunta = $this->mostrarPregunta();
        $_SESSION['tiempoEnvio'] = new DateTime("now");
        $this->manageSessionPartida($pregunta["id"]);
    }

    private function manageSessionPartida($preguntaId)
    {
        if (!isset($_SESSION['partida']))
            $_SESSION['partida'] = $this->model->addPartida($_SESSION['user']['username'], $preguntaId, $_SESSION['puntuacion']);
        else{
            $this->model->updatePartida($_SESSION['partida']['id'], $preguntaId, $_SESSION['puntuacion']);
        }
    }

    private function renderPartidaView($pregunta, $respuestas)
    {
        $this->presenter->render("view/player/partidaView.mustache", ["pregunta" => $pregunta, "respuestas" => $respuestas]);
    }

    public function answer()
    {
        $tiempoActual = new DateTime("now");
        $timer = $tiempoActual->getTimestamp() - $_SESSION['tiempoEnvio']->getTimestamp();
        if($timer > 30){
            $_SESSION['game_over'] = true;
            unset($_SESSION['partida']);
            unset($_SESSION['puntuacion']);
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
                $this->get();
            } else {
                $_SESSION['game_over'] = true;
                unset($_SESSION['partida']);
                unset($_SESSION['puntuacion']);

                $this->presenter->render("view/player/partidaView.mustache", ["game_over" => true]);
            }
        } else {
            $this->presenter->render("view/player/partidaView.mustache", ["error" => "Respuesta no válida."]);
        }
    }

    public function reset()
    {
        $_SESSION['game_over'] = false;
        $this->get();
    }

    private function mostrarPregunta()
    {
        $pregunta = $this->model->getPregunta($_SESSION['user']['username']);

        if (!$pregunta) {
            echo "Vista: no se pudo obtener pregunta";
        }

        $respuestas = $this->model->getRespuestas($pregunta["id"]);
        if (!$respuestas) {
            echo "Vista: no se pudieron obtener respuestas";
        }

        $this->renderPartidaView($pregunta, $respuestas);

        return $pregunta;
    }

//    private function getUserRank(){
//    $username = $_SESSION['user']['username']
//        $rank = $this->model->getRank($username);
//        return $rank;
//    }
}
