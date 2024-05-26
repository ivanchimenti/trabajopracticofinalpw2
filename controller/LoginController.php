<?php

class LoginController
{
    private $presenter;
    private $database;

    public function __construct($presenter, $database)
    {
        $this->presenter = $presenter;
        $this->database = $database;
    }

    public function get()
    {
        $this->presenter->render("view/loginView.mustache");
    }

    public function post()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if ($this->authenticate($username, $password)) {
            $this->presenter->render("view/dashboardView.mustache");
        } else {
            $this->presenter->render("view/loginView.mustache", ['error' => 'Usuario o contraseña incorrectos']);
        }
    }

    private function authenticate($username, $password)
    {
        $query = $this->database->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $query->bind_param("ss", $username, $password);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            return true;
        }
        return false;
    }
}
