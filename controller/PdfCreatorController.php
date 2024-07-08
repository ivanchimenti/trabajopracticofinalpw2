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
        if (isset($_GET['imagen']) && isset($_GET['filtro'])) {
            $base64 = $this->generateBase64Image($_GET['imagen']);
            $fecha = date("d/m/Y");

            switch ($_GET['filtro']) {
                case 'Year':
                    $template = "view/pdftemplates/reporteYearView.mustache";
                    break;
                case 'Month':
                    $template = "view/pdftemplates/reporteMonthView.mustache";
                    break;
                case 'Week':
                    $template = "view/pdftemplates/reporteWeekView.mustache";
                    break;
                case 'Day':
                    $template = "view/pdftemplates/reporteDayView.mustache";
                    break;
                default:
                    $template = "view/pdftemplates/pruebaView.mustache";
                    break;
            }
            $html = $this->presenter->generateHtml($template, ["base64Image" => $base64, "fecha" => $fecha], true); // Render for PDF
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
