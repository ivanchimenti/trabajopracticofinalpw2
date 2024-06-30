<?php

class PdfCreatorController{
    private $presenter;
    private $pdfCreator;

    public function __construct($presenter, $pdfCreator)
    {
        $this->presenter = $presenter;
        $this->pdfCreator = $pdfCreator;
    }

    public function create()
    {
        $resultado = $_GET['data'];
        $html = $this->presenter->generateHtml("view/pdfTemplates/pruebaView.mustache", ["resultado" => $resultado]);
        $this->pdfCreator->create($html);
    }
}