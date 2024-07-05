<?php

class PdfCreatorController
{
    private $presenter;
    private $pdfCreator;

    public function __construct($presenter, $pdfCreator)
    {
        $this->presenter = $presenter;
        $this->pdfCreator = $pdfCreator;
    }

    public function create()
    {
        if (isset($_GET['data'])) {
            $base64 = $this->generateBase64Image($_GET['data']);
            $html = $this->presenter->generateHtml("view/pdftemplates/pruebaView.mustache", ["base64Image" => $base64], true); // Render for PDF
            $this->pdfCreator->create($html);
        } else {
            echo "Parámetro 'data' no encontrado.";
        }
    }

    public function generateBase64Image($filename)
    {
        $filePath = 'public/graficos/' . $filename;
        $type = pathinfo($filePath, PATHINFO_EXTENSION);
        $data = file_get_contents($filePath);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}
