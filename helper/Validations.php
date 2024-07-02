<?php

function validateSession($controller, $action)
{
    if($controller == "user" && ($action == "" || $action == "get") && isset($_SESSION['user'])) {
        if($_SESSION['user']['role'] != "u") {
            redirect("/user/errorView");
        }
        redirect("/user/lobby");
    }

    if($controller == "admin"  && isset($_SESSION['user'])) {
        if($_SESSION['user']['role'] != "a") {
            redirect("/user/errorView");
        }
    }

    if($controller == "pdfCreator"  && isset($_SESSION['user'])) {
        if($_SESSION['user']['role'] != "a") {
            redirect("/user/errorView");
        }
    }

    if($controller == "graficos"  && isset($_SESSION['user'])) {
        if($_SESSION['user']['role'] != "a") {
            redirect("/user/errorView");
        }
    }

    if($controller == "editor"  && isset($_SESSION['user'])) {
        if($_SESSION['user']['role'] != "e") {
            redirect("/user/errorView");
        }
    }

    if($controller == "admin" && !isset($_SESSION['user'])) {
        redirect("/user/errorView");
    }

    if($controller == "editor" && !isset($_SESSION['user'])) {
        redirect("/user/errorView");
    }

    if($controller == "pdfCreator" && !isset($_SESSION['user'])) {
        redirect("/user/errorView");
    }

    if($controller == "graficos" && !isset($_SESSION['user'])) {
        redirect("/user/errorView");
    }

}
