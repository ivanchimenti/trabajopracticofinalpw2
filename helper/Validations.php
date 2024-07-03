<?php

function validateSession($controller, $action)
{
    if($controller == "user" && ($action == "" || $action == "get") && isset($_SESSION['user'])) {
        if($_SESSION['user']['role'] != "u") {
            redirect("user/errorView/error=403");
        }
        redirect("/user/lobby");
    }

    if($controller == "admin"  && isset($_SESSION['user'])) {
        if($_SESSION['user']['role'] != "a") {
            redirect("user/errorView/error=403");
        }
    }

    if($controller == "pdfCreator"  && isset($_SESSION['user'])) {
        if($_SESSION['user']['role'] != "a") {
            redirect("user/errorView/error=403");
        }
    }

    if($controller == "graficos"  && isset($_SESSION['user'])) {
        if($_SESSION['user']['role'] != "a") {
            redirect("user/errorView/error=403");
        }
    }

    if($controller == "editor"  && isset($_SESSION['user'])) {
        if($_SESSION['user']['role'] != "e") {
            redirect("user/errorView/error=403");
        }
    }

    if($controller == "admin" && !isset($_SESSION['user'])) {
        redirect("user/errorView/error=403");
    }

    if($controller == "editor" && !isset($_SESSION['user'])) {
        redirect("user/errorView/error=403");
    }

    if($controller == "pdfCreator" && !isset($_SESSION['user'])) {
        redirect("user/errorView/error=403");
    }

    if($controller == "graficos" && !isset($_SESSION['user'])) {
        redirect("user/errorView/error=403");
    }

    if($controller == "user" && $action == "profile" && !isset($_SESSION['user'])) {
        redirect("user/errorView/error=403");
    }

}
