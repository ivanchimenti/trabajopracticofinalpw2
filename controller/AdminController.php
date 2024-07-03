<?php

class AdminController
{
    private $presenter;
    private $model;
    private $graficosCreator;

    public function __construct($presenter, $model, $graficosCreator)
    {
        $this->presenter = $presenter;
        $this->model = $model;
        $this->graficosCreator = $graficosCreator;
    }

    public function get()
    {
        $data = [];
        $this->presenter->render("view/admin/adminView.mustache", $data);
    }

    public function listReports(){

        if (isset($_POST['filtro'])) {
            $filtro = $_POST['filtro'];
        } else {
            $filtro = 'Year';
        }

        $data['jugador'] = $this->model->cantidadDeUsuarios($filtro);
        $data['genero'] = $this->model->cantidadJugadoresPorGenero($filtro);
          $this->graficosCreator->getGraficoBarra($data['jugador'], 'cantidadJugadores.png', 'Cantidad de Jugadores');
          $this->graficosCreator->getGraficoLinea($data['genero'], 'cantidadPorGenero.png', 'Jugadores por genero');

        $data['jugador']['imagen_grafico'] = 'cantidadJugadores.png';
        $data['genero']['imagen_grafico'] = 'cantidadPorGenero.png';

        $this->presenter->render("view/admin/adminView.mustache", ["data" => $data]);
    }

    public function logout()
    {
        session_destroy();
        redirect('/user');
    }
}
