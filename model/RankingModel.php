<?php

class RankingModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function getRanking()
    {
        $query = $this->database->prepare("SELECT username, MAX(puntuacion) AS puntuacion FROM partida GROUP BY username ORDER BY puntuacion DESC;");
        $query->execute();
        $result = $query->get_result();

        $ranking = [];
        $position = 1;
        while ($row = $result->fetch_assoc()) {
            $row['posicion'] = $position++;
            $ranking[] = $row;
        }

        return $ranking;
    }

}

