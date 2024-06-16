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
        var_dump($_SESSION['user']);
        var_dump($_SESSION['puntuacion']);
        var_dump($_SESSION['tiempoEnvio']);
        var_dump($_SESSION['partida']);
        var_dump($_SESSION['ult_pregunta']);

        if (!isset($_SESSION['user'])) {
            header("Location: index.php");
            return;
        }

        if (isset($_SESSION['tiempoEnvio'])) {

            $tiempoActual = new DateTime("now");
            $timer = $tiempoActual->getTimestamp() - $_SESSION['tiempoEnvio']->getTimestamp();

            if ($timer <= 30) {

                $respuestaId = $_POST['respuesta_id'];
                $respuesta = $this->model->getRespuesta($respuestaId);

                if ($respuesta) {

                    $this->model->addPreguntaRespondida($respuesta['idPregunta'], $_SESSION['user']['username']);

                    $correcta = $respuesta['correcta'] == 1;

                    if ($correcta) {
                        echo "estoy entrando al if de correcta";
                        $_SESSION['puntuacion'] += 1;
                        $pregunta = $this->model->getPregunta($_SESSION['user']['username']);

                        $this->model->updatePartida($_SESSION['partida']['id'], $_SESSION['ult_pregunta'],$_SESSION['puntuacion']);

                        if ($pregunta) {
                            $respuestas = $this->model->getRespuestas($pregunta["id"]);

                            $_SESSION['ult_pregunta'] = $pregunta["id"];
                            $_SESSION['tiempoEnvio'] = new DateTime("now");

                            $this->presenter->render("view/partidaView.mustache", ["pregunta" => $pregunta, "respuestas" => $respuestas]);
                        } else {
                            unset($_SESSION['tiempoEnvio']);
                            unset($_SESSION['ult_pregunta']);
                            unset($_SESSION['puntuacion']);
                            unset($_SESSION['partida']);
                            $this->presenter->render("view/partidaView.mustache", ["no_more_questions" => true]);
                        }
                    } else {
                        unset($_SESSION['tiempoEnvio']);
                        unset($_SESSION['ult_pregunta']);
                        unset($_SESSION['puntuacion']);
                        unset($_SESSION['partida']);
                        $this->presenter->render("view/partidaView.mustache", ["game_over" => true]);
                    }
                } else {
                    unset($_SESSION['tiempoEnvio']);
                    unset($_SESSION['ult_pregunta']);
                    unset($_SESSION['puntuacion']);
                    unset($_SESSION['partida']);
                    $this->presenter->render("view/partidaView.mustache", ["error" => "Respuesta no válida."]);
                }
            } else {
                unset($_SESSION['tiempoEnvio']);
                unset($_SESSION['ult_pregunta']);
                unset($_SESSION['puntuacion']);
                unset($_SESSION['partida']);
                $this->presenter->render("view/partidaView.mustache", ["game_over" => true,"out_of_time" => true, "timer" => $timer]);
            }
        }
        else{
            unset($_SESSION['ult_pregunta']);

            $pregunta = $this->model->getPregunta($_SESSION['user']['username']);

            if ($pregunta) {
                $respuestas = $this->model->getRespuestas($pregunta["id"]);
                $_SESSION['ult_pregunta'] = $pregunta["id"];
                $_SESSION['tiempoEnvio'] = new DateTime("now");
                $this->model->addPartida($_SESSION['user']['username'], $pregunta["id"],$_SESSION['puntuacion']);
                $_SESSION['partida'] = $this->model->getPartidaActual($_SESSION['user']['username']);
                if ($_SESSION['partida']) {
                    $idPartida = $_SESSION['partida']; // Acceder al ID de la partida
                    // Otros valores de $this->partida si es necesario
                } else {
                    unset($_SESSION['tiempoEnvio']);
                    unset($_SESSION['ult_pregunta']);
                    unset($_SESSION['puntuacion']);
                    unset($_SESSION['partida']);
                    $this->presenter->render("view/partidaView.mustache", ["error" => "Respuesta no válida."]);
                }
                $this->presenter->render("view/partidaView.mustache", ["pregunta" => $pregunta, "respuestas" => $respuestas]);
            } else {
                unset($_SESSION['tiempoEnvio']);
                unset($_SESSION['ult_pregunta']);
                unset($_SESSION['puntuacion']);
                unset($_SESSION['partida']);
                $this->presenter->render("view/partidaView.mustache", ["no_more_questions" => true]);
            }
        }
    }

//    public function get()
//    {
//        if ($_SESSION['game_over']) {
//            $this->presenter->render("view/partidaView.mustache", ["game_over" => true]);
//            return;
//        }
//
//        $user = $_SESSION['user'];
//        $pregunta = $this->model->getPregunta($user['username']);
//
//        if($pregunta){
//            $respuestas = $this->model->getRespuestas($pregunta["id"]);
//            $_SESSION['pregunta_mostrada'] = $pregunta["id"];
//            $_SESSION['tiempo'] = new DateTime("now");
//            $this->presenter->render("view/partidaView.mustache", ["pregunta" => $pregunta, "respuestas" => $respuestas]);
//        } else {
//            $this->presenter->render("view/partidaView.mustache", ["no_more_questions" => true]);
//        }
//    }
//
//    public function answer()
//    {
//        define("MAXSEG", 30);
//
//        $actualTime = new DateTime("now");
//        $timer = $actualTime->getTimestamp() - $_SESSION['tiempo']->getTimestamp();
//
//        $respuestaId = $_POST['respuesta_id'];
//        $respuesta = $this->model->getRespuesta($respuestaId);
//        $user = $_SESSION['user'];
//
//        if($timer <= MAXSEG){
//            if ($respuesta) {
//
//                $this->model->addPreguntaRespondida($respuesta['idPregunta'],$user['username']);
//                $correcta = $respuesta['correcta'] == 1;
//
//                if ($correcta) {
//
//                    $pregunta = $this->model->getPregunta($user['username']);
//
//                    if ($pregunta) {
//                        $respuestas = $this->model->getRespuestas($pregunta["id"]);
//                        $_SESSION['pregunta_mostrada'] = $pregunta["id"];
//                        $_SESSION['tiempo'] = new DateTime("now");
//                        $this->presenter->render("view/partidaView.mustache", ["pregunta" => $pregunta, "respuestas" => $respuestas]);
//                    } else {
//                        $this->presenter->render("view/partidaView.mustache", ["no_more_questions" => true]);
//                    }
//                } else {
//                    $this->presenter->render("view/partidaView.mustache", ["game_over" => true]);
//                }
//            } else {
//                $this->presenter->render("view/partidaView.mustache", ["error" => "Respuesta no válida."]);
//            }
//        }
//        else{
//            $this->presenter->render("view/partidaView.mustache", ["game_over" => true,"out_of_time" => true, "timer" => $timer]);
//        }
//
//    }

    public function reset()
    {
        $_SESSION['game_over'] = false;
        $this->get();
    }
}
