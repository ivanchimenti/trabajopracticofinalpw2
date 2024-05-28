<?php


class Router
{
    private $defaultController;
    private $defaultMethod;

    public function __construct($defaultController, $defaultMethod)
    {
        $this->defaultController = $defaultController;
        $this->defaultMethod = $defaultMethod;
    }

    public function route($module, $httpMethod)
    {
        if ($httpMethod === 'get' && isset($_GET['action']) && $_GET['action'] === 'activate') {
            $controller = $this->getControllerFrom('user');
            $controller->activate();
            return;
        }

        $controller = $this->getControllerFrom($module);
        $this->executeMethodFromController($controller, $httpMethod);
    }

    private function getControllerFrom($module)
    {
        $controllerName = 'get' . ucfirst($module) . 'Controller';
        $validController = method_exists("Configuration", $controllerName) ? $controllerName : $this->defaultController;
        return call_user_func(array("Configuration", $validController));
    }

    private function executeMethodFromController($controller, $httpMethod)
    {
        $validMethod = method_exists($controller, $httpMethod) ? $httpMethod : $this->defaultMethod;
        call_user_func(array($controller, $validMethod));
    }
}
