<?php
use Dompdf\Dompdf;
class PdfCreator
{

    public function __construct()
    {
    }
    public function create($html)
    {
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("documentoNuevo.pdf", ['Attachment' => 0]);
    }
}
