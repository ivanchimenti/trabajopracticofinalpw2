<?php

class RankingModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function getRanking() {
        return $this->database->query("SELECT username, MAX(puntuacion) AS puntuacion
            FROM partida
            GROUP BY username order by puntuacion desc;");
//        return $this->database->query("SELECT ROW_NUMBER() OVER (ORDER BY puntuacion DESC) AS posicion, username, MAX(puntuacion) AS puntuacion
//            FROM partida
//            GROUP BY username;");
    }
}

