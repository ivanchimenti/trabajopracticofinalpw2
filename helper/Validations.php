<?php

function validateSession($controller, $action)
{
    if($controller == "user" && $action == "home" && !isset($_SESSION['user'])) {
        header("Location: /user");
        exit();
    }

}
