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
        $this->presenter->render("view/loginView.mustache");
    }

    public function post()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if ($this->model->authenticate($username, $password)) {
            $this->presenter->render("view/dashboardView.mustache");
        } else {
            $this->presenter->render("view/loginView.mustache", ['error' => 'Usuario o contraseña incorrectos']);
        }
    }
}
