<?php

class PartidaModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function getPregunta($preguntasMostradas)
    {

        $ids = implode(',', $preguntasMostradas);

        if(empty($ids)) {
            $query =  $this->database->prepare("SELECT * FROM Pregunta ORDER BY RAND() LIMIT 1");
        } else {
            $query =  $this->database->prepare("SELECT * FROM Pregunta WHERE id NOT IN ($ids) ORDER BY RAND() LIMIT 1");
        }

        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
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
}
