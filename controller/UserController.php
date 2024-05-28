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

    public function login()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $data = [];

        if ($this->model->login($username, $password)) {
            $_SESSION['username'] = $username;
            header('Location: /user/home');
            exit();
        } else {
            $data['error'] = 'Usuario o contraseña incorrectos';
            $this->presenter->render("view/loginView.mustache", $data);
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: /user');
        exit();
    }

    public function home()
    {
        $data = [];
        $this->presenter->render("view/homeView.mustache", $data);
    }

    public function register()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $data = [];

        $this->model->register($username, $password);
        $data['success'] = 'Usuario registrado exitosamente. Por favor, verifica tu correo electrónico para activar tu cuenta.';
        $this->presenter->render("view/loginView.mustache", $data);
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
