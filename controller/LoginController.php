<?php

class LoginController
{
    private $presenter;

    public function __construct($presenter)
    {
        $this->presenter = $presenter;
    }

    public function get()
    {
        $this->presenter->render("view/loginView.mustache");
    }

    public function post($username, $password)
    {
        //logica para autenticar
        if ($this->authenticate($username, $password)) {
            //cambiar al view adecuado
            $this->presenter->render("view/dashboardView.mustache");
        } else {

            $this->presenter->render("view/loginView.mustache", ['error' => 'usuario o contraseña incorrectos']);
        }
    }

    private function authenticate($username, $password)
    {
        if ($username === 'admin' && $password === 'password') {
            return true;
        }
        return false;
    }
}
