<?php

class AdminController
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
        $data = [];
        $this->presenter->render("view/adminView.mustache", $data);
    }

    public function logout()
    {
        session_destroy();
        header('Location: /user');
        exit();
    }
}
