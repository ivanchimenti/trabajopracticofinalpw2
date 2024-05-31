<?php

function validateSession($controller, $action)
{
    if($controller == "user" && $action == "get" && isset($_SESSION['user'])) {
        header("Location: /user/lobby");
        exit();
    }

    if($controller == "user" && $action == "lobby" && !isset($_SESSION['user'])) {
        header("Location: /user");
        exit();
    }

    if($controller == "user" && $action == "register" && isset($_SESSION['user'])) {
        header("Location: /user/lobby");
        exit();
    }

    if($controller == "user" && $action == "profile" && !isset($_SESSION['user'])) {
        header("Location: /user");
        exit();
    }

}
