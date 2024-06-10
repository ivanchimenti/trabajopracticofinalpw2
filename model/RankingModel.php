<?php

class RankingModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function getRanking() {
        $query = $this->database->prepare("SELECT username, score FROM user ORDER BY score DESC");
        $query->execute();
        $result = $query->get_result();

        if($result->num_rows > 0){
            $row = $result -> fetch_Assoc();
           return $row;
        }
        return null;
    }
}
