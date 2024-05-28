<?php

function validateSession($controller, $action)
{
    if($controller == "user" && $action == "home" && !isset($_SESSION['username'])) {
        header("Location: /user");
        exit();
    }

}
