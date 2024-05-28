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
            if ($this->model->login($username, $password)) {
                $this->presenter->render("view/homeView.mustache", $data);
            } else {
                $data['error'] = 'Usuario o contraseña incorrectos';
                $this->presenter->render("view/loginView.mustache", $data);
            }
        } elseif ($action === 'register') {
            $this->model->register($username, $password);
            $data['success'] = 'Usuario registrado exitosamente. Por favor, verifica tu correo electrónico para activar tu cuenta.';
            $this->presenter->render("view/loginView.mustache", $data);
        }
    }

    public function activate()
    {
        if (!isset($_GET['token'])) {
            $data = ['error' => 'Token de activación no proporcionado.'];
            $this->presenter->render("view/loginView.mustache", $data);
            return;
        }

        $authToken = $_GET['token'];
        $data = [];

        if ($this->model->activateAccount($authToken)) {
            $data['success'] = 'Cuenta activada exitosamente.';
        } else {
            $data['error'] = 'Token de activación inválido o la cuenta ya ha sido activada.';
        }
        $this->presenter->render("view/loginView.mustache", $data);
    }
}
