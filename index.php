<?php

include_once("Configuration.php");
$router = Configuration::getRouter();

$controller = isset($_GET["controller"]) ? $_GET["controller"] : "login";
$httpMethod = $_SERVER['REQUEST_METHOD'] === 'POST' ? 'post' : 'get';

$router->route($controller, $httpMethod);
