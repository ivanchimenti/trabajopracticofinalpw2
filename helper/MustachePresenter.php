<?php

class MustachePresenter
{
    private $mustache;
    private $partialsPathLoader;

    public function __construct($partialsPathLoader)
    {
        Mustache_Autoloader::register();
        $this->mustache = new Mustache_Engine(
            array(
                'partials_loader' => new Mustache_Loader_FilesystemLoader($partialsPathLoader)
            )
        );
        $this->partialsPathLoader = $partialsPathLoader;
    }

    public function render($contentFile, $data = array())
    {
        echo $this->generateHtml($contentFile, $data);
    }

    public function generateHtml($contentFile, $data = array())
    {
        $headerFile = $this->partialsPathLoader . '/header.mustache'; // Default header file

        if (isset($_SESSION['user']['role'])) {
            switch ($_SESSION['user']['role']) {
                case 'u':
                    $headerFile = $this->partialsPathLoader . '/headerUser.mustache';
                    break;
                case 'e':
                    $headerFile = $this->partialsPathLoader . '/headerEditor.mustache';
                    break;
                case 'a':
                    $headerFile = $this->partialsPathLoader . '/headerAdmin.mustache';
                    break;
                default:
                    $headerFile = $this->partialsPathLoader . '/header.mustache';
                    break;
            }
        }
        if (file_exists($headerFile)) {
            $contentAsString = file_get_contents($headerFile);
        } else {
            $contentAsString = file_get_contents($this->partialsPathLoader . '/header.mustache');
        }

        $contentAsString .= file_get_contents($contentFile);
        return $this->mustache->render($contentAsString, $data);
    }
}
