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
        redirect('/user');
    }

    public function toggleQuestionState()
    {
        $id = $_GET['id'];
        $this->model->toggleQuestionState($id);

        redirect('/editor');
    }

    public function deleteQuestion()
    {
        $idPregunta = $_GET['id'];
        $this->model->deleteQuestion($idPregunta);

        redirect('/editor');
    }

    public function manageQuestionView()
    {
        $idPregunta = isset($_GET['id']) ? $_GET['id'] : null;

        $data = [];
        if ($idPregunta) {
            $data['pregunta'] = $this->model->getQuestionById($idPregunta);
            $data['respuestas'] = $this->model->getAnswers($idPregunta);
            $data['categoriaSeleccionada'] = $data['pregunta']['categoria'];
        } else {
            $data['respuestas'] = [
                ['index' => 0, 'contenido' => '', 'correcta' => 0],
                ['index' => 1, 'contenido' => '', 'correcta' => 0],
                ['index' => 2, 'contenido' => '', 'correcta' => 0],
                ['index' => 3, 'contenido' => '', 'correcta' => 0],
            ];
            $data['categoriaSeleccionada'] = 1;
        }

        $this->presenter->render("view/editor/manageQuestionView.mustache", $data);
    }

    public function manageQuestion($suggestion = null)
    {
        $idPregunta = isset($_GET['id']) ? $_GET['id'] : null;
        $pregunta = $_POST['pregunta'];
        $categoria = $_POST['categoria'];
        $respuestas = array_values($_POST['respuestas']);
        $correctaIndex = $_POST['correcta'];

        foreach ($respuestas as $index => &$respuesta) {
            $respuesta['correcta'] = ($index == $correctaIndex) ? 1 : 0;
        }

        if($suggestion == "suggestion") {
            $this->model->addQuestion($categoria, $pregunta, $respuestas);
            redirect('/editor');
        }

        if ($idPregunta) {
            $this->model->updateQuestion($idPregunta, $categoria, $pregunta, $respuestas);
        } else {
            $this->model->addQuestion($categoria, $pregunta, $respuestas);
        }

        redirect('/editor');
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

        redirect('/editor/suggestionsView');
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
        $this->manageQuestion("suggestion");
    }

    public function rejectReport()
    {
        $idReporte = $_GET['id'];
        $this->model->rejectReport($idReporte);

        redirect('/editor/reportsView');
    }

    public function reportsView()
    {
        $reports = $this->model->listReports();
        $data = ['reports' => $reports];
        $this->presenter->render("view/editor/reportsView.mustache", $data);
    }

}
