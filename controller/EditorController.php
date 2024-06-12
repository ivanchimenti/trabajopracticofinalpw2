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

    public function deleteQuestion($id)
    {
        $this->model->deleteQuestion($id);
        header('Location: /editor');
        exit();
    }

    public function addQuestion()
    {
        // Aquí deberías manejar el formulario de agregar pregunta
        // Este es un ejemplo simple
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pregunta = $_POST['pregunta'];
            $respuestas = $_POST['respuestas']; // Este debería ser un array de respuestas
            $this->model->addQuestion($pregunta, $respuestas);
            header('Location: /editor');
            exit();
        }

        $data = [];
        $this->presenter->render("view/addQuestionView.mustache", $data);
    }
}
