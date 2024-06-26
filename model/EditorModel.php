<?php

class EditorModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function getQuestions()
    {
        $query = $this->database->prepare("SELECT * FROM Pregunta");
        $query->execute();
        $result = $query->get_result();

        $questions = array();

        while ($row = $result->fetch_assoc()) {
            $questions[] = $row;
        }

        return $questions;
    }

    public function getQuestionById($idPregunta)
    {
        $query = $this->database->prepare("SELECT * FROM Pregunta WHERE id = ?");
        $query->bind_param("i", $idPregunta);
        $query->execute();
        $result = $query->get_result();
        return $result->fetch_assoc();
    }

    public function toggleQuestionState($idPregunta)
    {
        $query = $this->database->prepare("UPDATE Pregunta SET estado = NOT estado WHERE id = ?");
        $query->bind_param("i", $idPregunta);
        $query->execute();
    }

    public function deleteQuestion($idPregunta)
    {
        $query = $this->database->prepare("DELETE FROM Pregunta_respondida WHERE id_pregunta = ?");
        $query->bind_param("i", $idPregunta);
        $query->execute();

        $query = $this->database->prepare("DELETE FROM Respuesta WHERE idPregunta = ?");
        $query->bind_param("i", $idPregunta);
        $query->execute();

        $query = $this->database->prepare("DELETE FROM Pregunta WHERE id = ?");
        $query->bind_param("i", $idPregunta);
        $query->execute();
    }

    public function addQuestion($categoria, $pregunta, $respuestas)
    {
        $query = $this->database->prepare("INSERT INTO Pregunta (categoria, contenido, estado) VALUES (?, ?, ?)");
        $estado = 1;
        $query->bind_param("ssi", $categoria, $pregunta, $estado);
        $query->execute();

        $idPregunta = $query->insert_id;

        foreach ($respuestas as $respuesta) {
            $contenido = $respuesta['contenido'];
            $correcta = $respuesta['correcta'];
            $query = $this->database->prepare("INSERT INTO Respuesta (contenido, idPregunta, correcta) VALUES (?, ?, ?)");
            $query->bind_param("sii", $contenido, $idPregunta, $correcta);
            $query->execute();
        }
    }

    public function updateQuestion($idPregunta, $categoria, $pregunta, $respuestas)
    {
        $query = $this->database->prepare("UPDATE Pregunta SET categoria = ?, contenido = ? WHERE id = ?");
        $query->bind_param("isi", $categoria, $pregunta, $idPregunta);
        $query->execute();

        $query = $this->database->prepare("DELETE FROM Respuesta WHERE idPregunta = ?");
        $query->bind_param("i", $idPregunta);
        $query->execute();

        foreach ($respuestas as $respuesta) {
            $contenido = $respuesta['contenido'];
            $correcta = $respuesta['correcta'];
            $query = $this->database->prepare("INSERT INTO Respuesta (contenido, idPregunta, correcta) VALUES (?, ?, ?)");
            $query->bind_param("sii", $contenido, $idPregunta, $correcta);
            $query->execute();
        }
    }

    public function getAnswers($idPregunta)
    {
        $query = $this->database->prepare("SELECT * FROM Respuesta WHERE idPregunta = ?");
        $query->bind_param("i", $idPregunta);
        $query->execute();
        $result = $query->get_result();

        $answers = array();
        $i = 0;

        while ($row = $result->fetch_assoc()) {
            $row['index'] = $i++;
            $answers[] = $row;
        }

        return $answers;
    }

    public function getSuggestions()
    {
        $query = $this->database->prepare("SELECT * FROM Sugerencia WHERE estado = 0");
        $query->execute();
        $result = $query->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function rejectSuggestion($idSugerencia)
    {
        $query = $this->database->prepare("DELETE FROM Sugerencia WHERE id = ?");
        $query->bind_param("i", $idSugerencia);
        $query->execute();
    }

    public function alterSuggestionState($idSugerencia)
    {
        $query = $this->database->prepare("UPDATE Sugerencia SET estado = NOT estado WHERE id = ?");
        $query->bind_param("i", $idSugerencia);
        $query->execute();
    }

    public function getSuggestionById($idSugerencia)
    {
        $query = $this->database->prepare("SELECT * FROM Sugerencia WHERE id = ?");
        $query->bind_param("i", $idSugerencia);
        $query->execute();
        $result = $query->get_result();
        return $result->fetch_assoc();
    }

    public function listReports()
    {
        $query = $this->database->prepare("SELECT DISTINCT id_pregunta FROM reporte");
        $query->execute();
        $result = $query->get_result();

        $reports = array();
        while ($row = $result->fetch_assoc()) {
            $reports[] = $row;
        }

        return $reports;
    }

    public function listReportById()
    {
        $query = $this->database->prepare("SELECT * FROM reporte WHERE id_pregunta = ?");
        $query->bind_param("i", $questionId);
        $query->execute();
        $result = $query->get_result();

        $reports = array();
        while ($row = $result->fetch_assoc()) {
            $reports[] = $row;
        }

        return $reports;
    }
}
