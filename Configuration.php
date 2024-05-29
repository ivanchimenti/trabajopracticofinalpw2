<?php

include_once("controller/PreguntadosController.php");
include_once("controller/UserController.php");
include_once("controller/PartidaController.php");

include_once("model/UserModel.php");
include_once("model/PartidaModel.php");

include_once("helper/Database.php");
include_once("helper/Router.php");
include_once("helper/Validations.php");

include_once("helper/Presenter.php");
include_once("helper/MustachePresenter.php");

include_once('vendor/mustache/src/Mustache/Autoloader.php');

class Configuration
{
    public static function getPreguntadosController(): PreguntadosController
    {
        return new PreguntadosController(self::getPresenter());
    }

    public static function getUserController(): UserController
    {
        return new UserController(self::getPresenter(), self::getUserModel());
    }

    public static function getPartidaController(): PartidaController
    {
        return new PartidaController(self::getPresenter(), self::getPartidaModel());
    }

    private static function getDatabase()
    {
        $config = self::getConfig();
        return new Database($config["servername"], $config["username"], $config["password"], $config["dbname"]);
    }

    private static function getUserModel(): UserModel
    {
        return new UserModel(self::getDatabase());
    }

    private static function getPartidaModel(): PartidaModel
    {
        return new PartidaModel(self::getDatabase());
    }

    private static function getConfig()
    {
        return parse_ini_file("config/config.ini");
    }

    public static function getRouter(): Router
    {
        return new Router("getUserController", "get");
    }

    private static function getPresenter(): MustachePresenter
    {
        return new MustachePresenter("view/template");
    }
}
