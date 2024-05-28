<?php

class UserController
{
    private $presenter;
    private $database;
    private $model;

    public function __construct($presenter, $database, $model)
    {
        $this->presenter = $presenter;
        $this->database = $database;
        $this->model = $model;
    }

    public function get()
    {
        $data = [];
        $this->presenter->render("view/loginView.mustache", $data);
    }

    public function post()
    {
        if (!isset($_POST['action'])) {
            $data = ['error' => 'Invalid request.'];
            $this->presenter->render("view/loginView.mustache", $data);
            return;
        }

        $action = $_POST['action'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $data = [];

        if ($action === 'login') {
            if ($this->model->authenticate($username, $password)) {
                $this->presenter->render("view/dashboardView.mustache", $data);
            } else {
                $data['error'] = 'Usuario o contraseña incorrectos';
                $this->presenter->render("view/loginView.mustache", $data);
            }
        } elseif ($action === 'register') {
            $this->model->register($username, $password);
            $data['success'] = 'Usuario registrado exitosamente.';
            $this->presenter->render("view/loginView.mustache", $data);
        }
    }
}
