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

    public function listReports()
    {
        $filtro = isset($_POST['filtro']) ? $_POST['filtro'] : 'Year';

        $data['jugador'] = $this->model->cantidadDeUsuarios($filtro);
        $data['genero'] = $this->model->cantidadJugadoresPorGenero($filtro);
        $data['partidas'] = $this->model->cantidadPartidasJugadas($filtro);
        $data['preguntas'] = $this->model->cantidadPreguntas($filtro);
        $data['porcentajeCorrecto'] = $this->model->porcentajeCorrectoPorJugador($filtro);
        $data['edad'] = $this->model->cantidadDeUsuariosPorEdad($filtro);

        $this->graficosCreator->getGraficoBarra($data['jugador'], 'cantidadJugadores.png', 'Cantidad de Jugadores');
        $this->graficosCreator->getGraficoLinea($data['genero'], 'cantidadPorGenero.png', 'Jugadores por género');
        $this->graficosCreator->getGraficoBarra($data['partidas'], 'cantidadPartidas.png', 'Cantidad de Partidas Jugadas');
        $this->graficosCreator->getGraficoBarraDoble($data['preguntas'], 'cantidadPreguntas.png', 'Preguntas Activas y Totales');
        $this->graficosCreator->getGraficoBarra($data['porcentajeCorrecto'], 'porcentajeCorrecto.png', 'Porcentaje de respuestas correctas');
        $this->graficosCreator->getGraficoPie($data['edad'], 'cantidadPorEdad.png', 'Cantidad de Jugadores por Edad');

        $data['jugador']['imagen_grafico'] = 'cantidadJugadores.png';
        $data['genero']['imagen_grafico'] = 'cantidadPorGenero.png';
        $data['partidas']['imagen_grafico'] = 'cantidadPartidas.png';
        $data['preguntas']['imagen_grafico'] = 'cantidadPreguntas.png';
        $data['porcentajeCorrecto']['imagen_grafico'] = 'porcentajeCorrecto.png';
        $data['edad']['imagen_grafico'] = 'cantidadPorEdad.png';

        $this->presenter->render("view/admin/adminView.mustache", ["data" => $data]);
    }


    public function logout()
    {
        session_destroy();
        redirect('/user');
    }
}
