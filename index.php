<?php

session_start();

include_once("Configuration.php");
$router = Configuration::getRouter();

$controller = isset($_GET["controller"]) ? $_GET["controller"] : "";
$action = isset($_GET["action"]) ? $_GET["action"] : "";
validateSession($controller, $action);

$router->route($controller, $action);
