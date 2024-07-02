<?php

include_once("controller/UserController.php");
include_once("controller/AdminController.php");
include_once("controller/EditorController.php");
include_once("controller/PartidaController.php");
include_once("controller/RankingController.php");
include_once("controller/PdfCreatorController.php");
include_once("controller/GraficosController.php");


include_once("model/UserModel.php");
include_once("model/AdminModel.php");
include_once("model/EditorModel.php");
include_once("model/PartidaModel.php");
include_once("model/RankingModel.php");
include_once("model/GraficosModel.php");

include_once("helper/Database.php");
include_once("helper/Router.php");
include_once("helper/redirect.php");
include_once("helper/Validations.php");
include_once("helper/Presenter.php");
include_once("helper/MustachePresenter.php");
include_once("helper/PdfCreator.php");
include_once("helper/GraficosCreator.php");

include_once('vendor/mustache/src/Mustache/Autoloader.php');
include_once('vendor/dompdf/autoload.inc.php');
include_once('vendor/jpgraph/src/jpgraph.php');
include_once('vendor/jpgraph/src/jpgraph_line.php');

class Configuration
{
    public static function getUserController(): UserController
    {
        return new UserController(self::getPresenter(), self::getUserModel(), self::getApiKey());
    }

    public static function getAdminController(): AdminController
    {
        return new AdminController(self::getPresenter(), self::getUserModel());
    }

    public static function getEditorController(): EditorController
    {
        return new EditorController(self::getPresenter(), self::getEditorModel());
    }

    public static function getPartidaController(): PartidaController
    {
        return new PartidaController(self::getPresenter(), self::getPartidaModel());
    }

    public static function getRankingController(): RankingController
    {
        return new RankingController(self::getPresenter(), self::getRankingModel());
    }

    public static function getPdfCreatorController(): PdfCreatorController
    {
        return new PdfCreatorController(self::getPresenter(), self::getPdfCreator());
    }

    public static function getGraficosController(): GraficosController
    {
        return new GraficosController(self::getPresenter(), self::getGraficosModel(), self::getGraficosCreator());
    }

    private static function getDatabase()
    {
        $config = self::getConfig();
        return new Database($config["servername"], $config["username"], $config["password"], $config["dbname"]);
    }

    private static function getApiKey()
    {
        $config = self::getConfig();
        return $config["googleAPIkey"];
    }

    private static function getUserModel(): UserModel
    {
        return new UserModel(self::getDatabase());
    }

    private static function getAdminModel(): AdminModel
    {
        return new AdminModel(self::getDatabase());
    }

    private static function getEditorModel(): EditorModel
    {
        return new EditorModel(self::getDatabase());
    }

    private static function getPartidaModel(): PartidaModel
    {
        return new PartidaModel(self::getDatabase());
    }

    private static function getRankingModel(): RankingModel
    {
        return new RankingModel(self::getDatabase());
    }
    private static function getGraficosModel(): GraficosModel
    {
        return new GraficosModel(self::getDatabase());
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

    private static function getPdfCreator(): PdfCreator
    {
        return new PdfCreator();
    }
    private static function getGraficosCreator(): GraficosCreator
    {
        return new GraficosCreator();
    }
}
