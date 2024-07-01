<?php
class GraficosCreator{

    public function __construct()
    {
    }
    public function create($datay1){

// Setup the graph
        $graph = new Graph(300, 250);
        $graph->SetScale("textlin");

// Create the first line
        $p1 = new LinePlot($datay1);
        $graph->Add($p1);

        $graph->legend->SetFrameWeight(1);

// Output line
        $graph->Stroke();
    }
}





