<?php

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfCreator
{
    public function __construct()
    {
    }

    public function create($html)
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("documentoNuevo.pdf", ['Attachment' => false]);
    }
}
