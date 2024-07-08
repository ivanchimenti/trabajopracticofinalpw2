<?php

class GraficosCreator
{
    public function __construct()
    {
    }
    public function create($datay1)
    {

        $graph = new Graph(350, 250);
        $graph->SetScale("textlin");

        $p1 = new LinePlot($datay1);
        $graph->Add($p1);

        $graph->legend->SetFrameWeight(1);

        $graph->Stroke('public/graficos/graph.png');
    }

    public function getGraficoPie($data, $filename, $title)
    {
        $data1y = array();
        $dataName = array();
        $total = 0;


        foreach ($data as $row) {
            $data1y[] = $row["cantidad"];
            $dataName[] = $row["rango_edad"] . ' total: ' . $row["cantidad"];
            $total += $row["cantidad"];
        }


        $graph = new PieGraph(350, 250);

        $graph->SetShadow();

        $graph->title->Set($title);

        $p1 = new PiePlot($data1y);
        $p1->SetLegends($dataName);
        $p1->SetCenter(0.5, 0.5);

        $graph->Add($p1);
        $nombreImg = 'public/graficos/' . $filename;
        $this->getImagenGrafico($graph, $nombreImg);
    }


    public function getGraficoBarra($data, $filename, $title)
    {

        $data1y = array();
        $dataName = array();

        for ($i = 0; $i < count($data); $i++) {
            $data1y[] = $data[$i]["cantidad"];
            $dataName[] = $data[$i]["filtro"];
        }

        $graph = $this->getGraph();
        $this->getConfigGraph($graph, $dataName);

        $b1plot = $this->getBarPlot($data1y);
        $graph->Add($b1plot);

        $graph->title->Set($title);
        $nombreImg = 'public/graficos/' . $filename;
        $img = $this->getImagenGrafico($graph, $nombreImg);
    }

    public function getGraficoBarraMultiple($data, $filename, $title)
    {
        $data1y=$data['female'];
        $data2y=$data['male'];
        $data3y=$data['unspecified'];

        $graph = new Graph(350,250,'auto');
        $graph->SetScale("textlin");

        $theme_class=new UniversalTheme;
        $graph->SetTheme($theme_class);

        $graph->SetBox(false);

        $graph->ygrid->SetFill(false);
        $graph->xaxis->SetTickLabels($data['filtro']);
        $graph->yaxis->HideLine(false);
        $graph->yaxis->HideTicks(false,false);

        $b1plot = new BarPlot($data1y);
        $b2plot = new BarPlot($data2y);
        $b3plot = new BarPlot($data3y);

        $gbplot = new GroupBarPlot(array($b1plot,$b2plot,$b3plot));
        $graph->Add($gbplot);

        $b1plot->SetColor("white");
        $b1plot->SetFillColor("#cc1111");

        $b2plot->SetColor("white");
        $b2plot->SetFillColor("#11cccc");

        $b3plot->SetColor("white");
        $b3plot->SetFillColor("#1111cc");

        $graph->title->Set($title);
        $nombreImg = 'public/graficos/' . $filename;
        $img = $this->getImagenGrafico($graph, $nombreImg);
    }

    public function getGraficoBarraAciertos($data, $filename, $title)
    {

        $data1y = array();
        $data2y= array();
        $dataName = array();

        for ($i = 0; $i < count($data); $i++) {
            $data1y[] = $data[$i]["cantidad"];
            $data2y[]=$data[$i]["erradas"];
            $dataName[] = $data[$i]["filtro"];
        }

        $graph = $this->getGraph();
        $this->getConfigGraph($graph, $dataName);

        $b1plot = $this->getBarPlot($data1y);
        $graph->Add($b1plot);

        $b1plot = new BarPlot($data1y);
        $b2plot = new BarPlot($data2y);

        $gbplot = new GroupBarPlot(array($b1plot,$b2plot));
        $graph->Add($gbplot);

        $b1plot->SetFillColor("#0000FF");
        $b2plot->SetFillColor("#FF0000");

        $graph->title->Set($title);
        $nombreImg = 'public/graficos/' . $filename;
        $img = $this->getImagenGrafico($graph, $nombreImg);
    }

    public function getGraficoBarraDoble($data, $filename, $title)
    {
        $data1y = array();
        $data2y = array();
        $dataName = array();

        for ($i = 0; $i < count($data); $i++) {
            $data1y[] = $data[$i]["preguntas_activas"];
            $data2y[] = $data[$i]["total_preguntas"];
            $dataName[] = $data[$i]["filtro"];
        }

        $graph = $this->getGraph();
        $this->getConfigGraph($graph, $dataName);

        $b1plot = $this->getBarPlot($data1y, "Activas", "blue");
        $b2plot = $this->getBarPlot($data2y, "Totales", "red");

        $gbplot = new GroupBarPlot(array($b1plot, $b2plot));
        $graph->Add($gbplot);

        $graph->title->Set($title);
        $nombreImg = 'public/graficos/' . $filename;
        $img = $this->getImagenGrafico($graph, $nombreImg);
    }

    public function getGraficoLinea($data, $fileName, $titulo)
    {
        $dataName = array();
        $data1y = array();
        $data2y = array();
        $data3y = array();
        $data4y = array();

        for ($i = 0; $i < count($data); $i++) {
            $dataName[] = $data[$i]["filtro"];
            $data1y[] = $data[$i]["filtro1"];

            switch ($data[$i]["filtro1"]) {
                case 'Femenino':
                    $data2y[] = $data[$i]["cantidad"];
                    break;
                case 'Masculino':
                    $data3y[] = $data[$i]["cantidad"];
                    break;
                case 'Sin Especificar':
                    $data4y[] = $data[$i]["cantidad"];
                    break;
            }
        }

        // ESTO ME ARREGLA EL GRAFICO DE LINEAS
        if (count($data2y) < 2) {
            $data2y[] = 0;
            $dataName[] = "";
        }

        if (count($data3y) < 2) {
            $data3y[] = 0;
            $dataName[] = "";
        }

        if (count($data4y) < 2) {
            $data4y[] = 0;
            $dataName[] = "";
        }

        $dataName = array_unique($dataName);
        $data1y = array_unique($data1y);

        $graph = new Graph(350, 250);
        $graph->SetScale("textlin");

        $theme_class = new UniversalTheme();

        $graph->SetTheme($theme_class);
        $graph->img->SetAntiAliasing(false);
        $graph->title->Set($titulo);
        $graph->SetBox(false);

        $graph->SetMargin(40, 20, 36, 63);

        $graph->img->SetAntiAliasing();

        $graph->yaxis->HideLine(false);
        $graph->yaxis->HideTicks(false, false);

        $graph->xgrid->Show();
        $graph->xgrid->SetLineStyle("solid");
        $graph->xaxis->SetTickLabels($dataName);
        $graph->xgrid->SetColor('#E3E3E3');

        if(count($data2y) > 0) {
            $p1 = new LinePlot($data2y);
            $graph->Add($p1);
            $p1->SetColor("#6495ED");
            $p1->SetLegend('Femenino');
        }

        if(count($data3y) > 0) {
            $p2 = new LinePlot($data3y);
            $graph->Add($p2);
            $p2->SetColor("#B22222");
            $p2->SetLegend('Masculino');
        }

        if(count($data4y) > 0) {
            $p3 = new LinePlot($data4y);
            $graph->Add($p3);
            $p3->SetColor("#FF1493");
            $p3->SetLegend('Sin Especificar');
        }

        $graph->legend->SetFrameWeight(1);

        $nombreImg = 'public/graficos/' . $fileName;
        $this->getImagenGrafico($graph, $nombreImg);
    }

    public function getGraficodeGenero($data, $fileName, $titulo){
        $years = $data['filtro'];
        $female = $data['female'];
        $male = $data['male'];
        $unspecified = $data['unspecified'];

// Crear el gráfico
        $graph = new Graph(800, 600);
        $graph->SetScale('textlin');

// Configurar títulos
        $graph->title->Set('Número de personas sumadas por año');
        $graph->xaxis->title->Set('Año');
        $graph->yaxis->title->Set('Cantidad de personas');
        $graph->xaxis->SetTickLabels($years);

// Crear las líneas
        $femalePlot = new LinePlot($female);
        $femalePlot->SetLegend('Femenino');
        $femalePlot->SetColor("#FF1493");

        $malePlot = new LinePlot($male);
        $malePlot->SetLegend('Masculino');
        $malePlot->SetColor("#6495ED");

        $unspecifiedPlot = new LinePlot($unspecified);
        $unspecifiedPlot->SetLegend('Sin especificar');
        $unspecifiedPlot->SetColor("#00FF00");

// Agregar las líneas al gráfico
        $graph->Add($femalePlot);
        $graph->Add($malePlot);
        $graph->Add($unspecifiedPlot);

// Mostrar la leyenda
        $graph->legend->SetFrameWeight(1);
        $graph->legend->SetColumns(1);
        $graph->legend->SetPos(0.5,0.98,'center','bottom');

// Mostrar el gráfico
        $nombreImg = 'public/graficos/' . $fileName;
        $this->getImagenGrafico($graph, $nombreImg);
    }

    public function getGraph()
    {
        $graph = new Graph(350, 250, 'auto');
        $graph->SetScale("textlin");
        $theme_class = new UniversalTheme();
        $graph->SetTheme($theme_class);

        return $graph;
    }

    public function getConfigGraph($graph, $dataName)
    {
        $graph->SetBox(false);
        $graph->ygrid->SetFill(false);
        $graph->xaxis->SetTickLabels($dataName);
        $graph->xaxis->SetLabelAngle(90);
        return $graph;
    }

    public function getBarPlot($data1y, $legend = null, $color = null)
    {
        $b1plot = new BarPlot($data1y);
        $b1plot->SetColor("white");
        $b1plot->SetFillColor("#cc1111");
        $b1plot->value->Show();

        if ($legend != null) {
            $b1plot->SetLegend($legend);
        }
        if ($color != null) {
            $b1plot->SetFillColor($color);
        }
        return $b1plot;
    }

    public function getImagenGrafico($graph, $fileName)
    {
        $graph->Stroke(_IMG_HANDLER);
        $image = $graph->img->Stream($fileName);
        return $image;
    }
}
