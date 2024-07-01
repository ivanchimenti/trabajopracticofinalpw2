<?php

class LoginModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function authenticate($username, $password)
    {
        $query = $this->database->prepare("SELECT * FROM user WHERE username = ? AND password = ?;");
        $query->bind_param("ss", $username, $password);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            return true;
        }
        return false;
    }
}