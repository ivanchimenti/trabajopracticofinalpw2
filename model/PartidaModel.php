<?php

class PartidaModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function reportQuestion($questionId, $username)
    {
        $query = $this->database->prepare("INSERT INTO reporte (id_pregunta, username) VALUES (?, ?)");
        $query->bind_param("is", $questionId, $username);
        return $query->execute();
    }

    public function getPregunta($username)
    {
        $percentage = $this->getRank($username);
        $range = $this->getDifficultyRange($percentage);
        $pregunta = $this->fetchPreguntaByUserAndRange($username, $range);

        if ($pregunta === null) {
            $pregunta = $this->fetchPreguntaByUser($username);
        }

        if ($pregunta === null) {
            $this->deletePreguntasRespondidas($username);
            //basicamente corre de nuevo el metodo para que se le asigne una pregunta
            $pregunta = $this->fetchPreguntaByUserAndRange($username, $range);

            if ($pregunta === null) {
                $pregunta = $this->fetchPreguntaByUser($username);
            }

        }

        if ($pregunta !== null) {
            $this->updatePreguntaRank($pregunta['id']);
            $this->addToPreguntaMostrada($pregunta['id']);
        }

        $this->addToUserCantEntregada($username);
        return $pregunta;
    }

    private function getRank($username)
    {
        $query = $this->database->prepare("SELECT cantEntregada, cantRespondida FROM user WHERE username LIKE ?;");
        $query->bind_param("s", $username);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if ($row['cantEntregada'] != 0) {
                $rank = ($row['cantRespondida'] / $row['cantEntregada']) * 100;
            } else {
                $rank = 0;
            }
            return $rank;
        } else {
            return null;
        }
    }

    private function getDifficultyRange($percentage)
    {
        if ($percentage >= 0 && $percentage <= 33) {
            return [0, 33];
        } elseif ($percentage > 33 && $percentage <= 66) {
            return [34, 66];
        } elseif ($percentage > 66 && $percentage <= 100) {
            return [67, 100];
        } else {
            return null;
        }
    }

    private function fetchPreguntaByUserAndRange($username, $range)
    {
        $query = $this->database->prepare("SELECT * FROM Pregunta p WHERE p.id NOT IN (SELECT pr.id_pregunta FROM Pregunta_Respondida pr WHERE pr.id_usuario = ?) AND estado = 1 AND porcentajeAcertado BETWEEN ? AND ? ORDER BY RAND() LIMIT 1;");
        $query->bind_param("sii", $username, $range[0], $range[1]);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    private function fetchPreguntaByUser($username)
    {
        $query = $this->database->prepare("SELECT * FROM Pregunta p WHERE p.id NOT IN (SELECT pr.id_pregunta FROM Pregunta_Respondida pr WHERE pr.id_usuario = ?) AND estado = 1 ORDER BY RAND() LIMIT 1;");
        $query->bind_param("s", $username);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    private function addToPreguntaMostrada($idPregunta)
    {
        $query = $this->database->prepare("UPDATE Pregunta SET cantEntregada = cantEntregada + 1 WHERE id = ?");
        $query->bind_param("i", $idPregunta);
        $query->execute();
    }

    public function getPreguntaById($idPregunta)
    {
        $query = $this->database->prepare("SELECT * FROM Pregunta WHERE id = ?");
        $query->bind_param("i", $idPregunta);
        $query->execute();
        $result = $query->get_result();

        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    public function getRespuestas($idPregunta)
    {
        $query =  $this->database->prepare("SELECT * FROM Respuesta WHERE idPregunta = ?");
        $query->bind_param("i", $idPregunta);
        $query->execute();
        $result = $query->get_result();

        $respuestas = array();

        while ($row = $result->fetch_assoc()) {
            $respuestas[] = $row;
        }

        if (count($respuestas) > 0) {
            return $respuestas;
        } else {
            return null;
        }
    }

    public function getRespuesta($idRespuesta)
    {
        $query = $this->database->prepare("SELECT * FROM Respuesta WHERE id = ?");
        $query->bind_param("i", $idRespuesta);
        $query->execute();
        $result = $query->get_result();

        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    public function addPreguntaRespondida($idPregunta, $idUsuario, $acierto)
    {
        $query = $this->database->prepare("INSERT INTO pregunta_respondida (id_pregunta,id_usuario,acierto) VALUES (?,?,?);");
        $query->bind_param("isi", $idPregunta, $idUsuario, $acierto);

        $this->addToPreguntaCantRespondida($idPregunta);
        $this->addToUserCantRespondida($idUsuario);
        $this->updatePreguntaRank($idPregunta);

        return $query->execute();
    }


    private function deletePreguntasRespondidas($idUsuario)
    {
        $query = $this->database->prepare("DELETE FROM pregunta_respondida WHERE id_usuario LIKE ?;");
        $query->bind_param("s", $idUsuario);
        $query->execute();
    }

    private function addToPreguntaCantRespondida($idPregunta)
    {
        $query = $this->database->prepare("UPDATE Pregunta SET cantRespondida = cantRespondida + 1 WHERE id = ?");
        $query->bind_param("i", $idPregunta);
        $query->execute();
    }

    private function updatePreguntaRank($idPregunta)
    {
        $query = $this->database->prepare("SELECT cantEntregada, cantRespondida FROM Pregunta WHERE id = ?");
        $query->bind_param("i", $idPregunta);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if ($row['cantEntregada'] != 0) {
                $porcentajeAcertado = ($row['cantRespondida'] / $row['cantEntregada']) * 100;
            } else {
                $porcentajeAcertado = 0;
            }

            $updateQuery = $this->database->prepare("UPDATE Pregunta SET porcentajeAcertado = ? WHERE id = ?");
            $updateQuery->bind_param("di", $porcentajeAcertado, $idPregunta);
            $updateQuery->execute();
        }
    }

    private function addToUserCantRespondida($idUsuario)
    {
        $query = $this->database->prepare("UPDATE user SET cantRespondida = cantRespondida + 1 WHERE username = ?");
        $query->bind_param("s", $idUsuario);
        $query->execute();
    }

    private function addToUserCantEntregada($idUsuario)
    {
        $query = $this->database->prepare("UPDATE user SET cantEntregada = cantEntregada + 1 WHERE username = ?");
        $query->bind_param("s", $idUsuario);
        $query->execute();
    }

    public function getPartidaActual($username)
    {
        $query = $this->database->prepare("SELECT MAX(id) as id, ult_pregunta as id_pregunta FROM partida WHERE username LIKE ? AND finalizada = 0;");
        $query->bind_param("s", $username);
        $query->execute();
        $result = $query->get_result();
        $partida = $result->fetch_assoc();
        return $partida ? $partida : null;
    }
    public function addPartida($idUsuario, $idPregunta, $puntuacion)
    {
        $query = $this->database->prepare("INSERT INTO partida (username, ult_pregunta,fecha_ingreso, puntuacion, finalizada) VALUES (?,?,NOW(),?, 0);");
        $query->bind_param("sii", $idUsuario, $idPregunta, $puntuacion);
        $query->execute();
        return $this->getPartidaActual($idUsuario);
    }

    public function updatePartida($idPartida, $idPregunta, $puntuacion)
    {
        $query = $this->database->prepare("UPDATE partida SET ult_pregunta = ?, puntuacion = ? WHERE id LIKE ?;");
        $query->bind_param("iii", $idPregunta, $puntuacion, $idPartida);
        return $query->execute();
    }

    public function endPartida($idPartida)
    {
        $query = $this->database->prepare("UPDATE partida SET finalizada = 1 WHERE id LIKE ?;");
        $query->bind_param("i", $idPartida);
        return $query->execute();
    }

}
