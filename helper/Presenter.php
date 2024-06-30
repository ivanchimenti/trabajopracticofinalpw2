<?php

class Presenter
{



    public function render($view, $data = [])
    {
        include_once("view/template/header.mustache");
        include_once($view);
    }
}
