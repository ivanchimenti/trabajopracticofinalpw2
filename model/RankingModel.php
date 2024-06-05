<?php

class RankingModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function getRanking() {
        $query = "SELECT username, score FROM user ORDER BY score DESC";
        $result = $this->database->query($query);

        $ranking = [];
        while ($row = $result->fetch_assoc()) {
            $ranking[] = $row;
        }

        return $ranking;
    }
}
