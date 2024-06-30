<?php

class EditorController
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
        $questions = $this->model->getQuestions();
        $data = ['questions' => $questions];
        $this->presenter->render("view/editor/editorView.mustache", $data);
    }

    public function logout()
    {
        session_destroy();
        header('Location: /user');
        exit();
    }

    public function toggleQuestionState()
    {
        $id = $_GET['id'];
        $this->model->toggleQuestionState($id);
        header('Location: /editor');
        exit();
    }

    public function deleteQuestion()
    {
        $idPregunta = $_GET['id'];
        $this->model->deleteQuestion($idPregunta);
        header('Location: /editor');
        exit();
    }

    public function manageQuestionView()
    {
        $idPregunta = isset($_GET['id']) ? $_GET['id'] : null;

        $data = [];
        if ($idPregunta) {
            $data['pregunta'] = $this->model->getQuestionById($idPregunta);
            $data['respuestas'] = $this->model->getAnswers($idPregunta);
        } else {
            $data['respuestas'] = [
                ['index' => 0, 'contenido' => '', 'correcta' => 0],
                ['index' => 1, 'contenido' => '', 'correcta' => 0],
                ['index' => 2, 'contenido' => '', 'correcta' => 0],
                ['index' => 3, 'contenido' => '', 'correcta' => 0],
            ];
        }

        $this->presenter->render("view/editor/manageQuestionView.mustache", $data);
    }

    public function manageQuestion()
    {
        $idPregunta = isset($_GET['id']) ? $_GET['id'] : null;
        $pregunta = $_POST['pregunta'];
        $categoria = $_POST['categoria'];
        $respuestas = array_values($_POST['respuestas']);
        $correctaIndex = $_POST['correcta'];

        foreach ($respuestas as $index => &$respuesta) {
            $respuesta['correcta'] = ($index == $correctaIndex) ? 1 : 0;
        }

        if ($idPregunta) {
            $this->model->updateQuestion($idPregunta, $categoria, $pregunta, $respuestas);
        } else {
            $this->model->addQuestion($categoria, $pregunta, $respuestas);
        }

        header('Location: /editor');
        exit();
    }

    public function suggestionsView()
    {
        $suggestions = $this->model->getSuggestions();
        $data = ['suggestions' => $suggestions];
        $this->presenter->render("view/editor/suggestionsView.mustache", $data);
    }

    public function rejectSuggestion()
    {
        $idSugerencia = $_GET['id'];
        $this->model->rejectSuggestion($idSugerencia);
        header('Location: /editor/suggestionsView');
        exit();
    }

    public function manageSuggestionView()
    {
        $idSugerencia = $_GET['id'];
        $suggestion = $this->model->getSuggestionById($idSugerencia);
        $data = ['suggestion' => $suggestion];
        $this->presenter->render("view/editor/manageSuggestionView.mustache", $data);
    }

    private function alterSuggestionState()
    {
        $idSugerencia = $_GET['id'];
        $this->model->alterSuggestionState($idSugerencia);
    }

    public function acceptSuggestion()
    {
        $this->alterSuggestionState();
        $this->manageQuestion();
    }

    public function reportsView()
    {
        $reports = $this->model->listReports();
        $data = ['reports' => $reports];
        $this->presenter->render("view/editor/reportsView.mustache", $data);
    }

}
