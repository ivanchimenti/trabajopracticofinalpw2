<?php

function validateSession($controller, $action)
{
    // Check if $_SESSION['user'] is set and not null
    if (!isset($_SESSION['user']) || $_SESSION['user'] === null) {
        $_SESSION['user'] = [];
    }

    if (!isset($_SESSION['user']['role'])) {
        $_SESSION['user']['role'] = null;
    }

    // Redirect logic based on controller, action, and user session
    if ($controller == "user") {
        switch ($action) {
            case "get":
            case "register":
                if (isset($_SESSION['user'])) {
                    header("Location: /user/lobby");
                    exit();
                }
                break;
            case "lobby":
                if (!isset($_SESSION['user'])) {
                    header("Location: /user");
                    exit();
                }
                break;
            case "profile":
                if (!isset($_SESSION['user'])) {
                    header("Location: /user");
                    exit();
                }
                break;
            default:
                break;
        }

        // Redirect based on user role
        if ($_SESSION['user']['role'] == "a") {
            header("Location: /admin");
            exit();
        }

        if ($_SESSION['user']['role'] == "e") {
            header("Location: /editor");
            exit();
        }

    }
}
