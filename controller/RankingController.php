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
        $ranking = $this->model->getRanking();
        $this->presenter->render("view/rankingView.mustache", ["ranking" => $ranking]);
    }
}

