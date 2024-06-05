<?php

class PartidaModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function getPregunta($username)
    {
//        $ids = implode(',', $preguntasMostradas);
//
//        if(empty($ids)) {
//            $query =  $this->database->prepare("SELECT * FROM Pregunta ORDER BY RAND() LIMIT 1");
//        } else {
//            $query =  $this->database->prepare("SELECT * FROM Pregunta WHERE id NOT IN ($ids) ORDER BY RAND() LIMIT 1");
//        }

        $query =  $this->database->prepare("SELECT * FROM Pregunta p WHERE p.id NOT IN (SELECT pr.id_pregunta FROM Pregunta_Respondida pr WHERE pr.id_usuario = ?) ORDER BY RAND() LIMIT 1;");
        $query->bind_param("s", $username);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            $this->deletePreguntasRespondidas($username);

            $query =  $this->database->prepare("SELECT * FROM Pregunta p WHERE p.id NOT IN (SELECT pr.id_pregunta FROM Pregunta_Respondida pr WHERE pr.id_usuario = ?) ORDER BY RAND() LIMIT 1;");
            $query->bind_param("s", $username);
            $query->execute();
            $result = $query->get_result();
            return $result->fetch_assoc();
        }
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

    public function addPreguntaRespondida($idPregunta,$idUsuario){

        $query = $this->database->prepare("SELECT * FROM pregunta_respondida WHERE id_usuario LIKE ? AND id_pregunta = ?;");
        $query->bind_param("si",$idUsuario, $idPregunta);
        $query->execute();
        $result = $query->get_result();

        if($result->num_rows != 0)
        {
            $this->deletePreguntasRespondidas($idUsuario);
        }

        $query = $this->database->prepare("INSERT INTO pregunta_respondida (id_pregunta,id_usuario) VALUES (?,?);");
        $query->bind_param("is", $idPregunta, $idUsuario);
        return $query->execute();
    }

    public function deletePreguntasRespondidas($idUsuario){
        $query = $this->database->prepare("DELETE FROM pregunta_respondida WHERE id_usuario LIKE ?;");
        $query->bind_param("s", $idUsuario);
        $query->execute();
    }
}
