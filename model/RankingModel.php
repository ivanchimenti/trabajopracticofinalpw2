<?php

class RankingModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function getRanking() {
        return $this->database->query("SELECT posicion, username, MAX(puntuacion) AS puntuacion
            FROM (
                SELECT ROW_NUMBER() OVER (ORDER BY puntuacion DESC) AS posicion, username, puntuacion
                FROM partida
            ) AS subconsulta
            GROUP BY username;");
    }
}
