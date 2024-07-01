<?php

function validateSession($controller, $action)
{
    if($controller == "user" && ($action == "" ||$action == "get") && isset($_SESSION['user'])) {
        if($_SESSION['user']['role'] != "u"){
            header("Location: /user/errorView");
            exit();
        }
        header("Location: /user/lobby");
        exit();
    }

    if($controller == "admin"  && isset($_SESSION['user'])) {
        if($_SESSION['user']['role'] != "a"){
            header("Location: /user/errorView");
            exit();
        }
    }

    if($controller == "pdfCreator"  && isset($_SESSION['user'])) {
        if($_SESSION['user']['role'] != "a"){
            header("Location: /user/errorView");
            exit();
        }
    }

    if($controller == "graficos"  && isset($_SESSION['user'])) {
        if($_SESSION['user']['role'] != "a"){
            header("Location: /user/errorView");
            exit();
        }
    }

    if($controller == "editor"  && isset($_SESSION['user'])) {
        if($_SESSION['user']['role'] != "e"){
            header("Location: /user/errorView");
            exit();
        }
    }

}
