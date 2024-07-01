<?php

class RankingController
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
        $resultado = $this->model->getRanking();
        $this->presenter->render("view/player/rankingView.mustache", ["ranking" => $resultado]);
    }
}

