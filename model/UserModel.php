<?php

class UserModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function login($username, $password)
    {
        $this->endAllPartidas($username);
        $query = $this->database->prepare("SELECT * FROM user WHERE username = ?");
        $query->bind_param("s", $username);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password'];
            $authToken = $row['authToken'];

            if ($authToken != "") {
                return null;
            }

            if (password_verify($password, $hashedPassword)) {
                unset($row['password']);
                return $row;
            }
        }
        return null;
    }

    public function getUserByUsername($username)
    {
        $query = $this->database->prepare("SELECT * FROM user WHERE username = ?");
        $query->bind_param("s", $username);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            unset($row['password']);
            return $row;
        }
        return null;
    }

    public function register($username, $hashedPassword, $authToken, $fullName, $birthYear, $gender, $latitude, $longitude, $email, $profilePicturePath)
    {
        $query = $this->database->prepare("INSERT INTO user (username, password, authToken, full_name, birth_year, gender, latitude, longitude, email, profile_picture, role, cantEntregada, cantRespondida,fecha_ingreso) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'u',10,5,CURDATE())");
        $query->bind_param("ssssisiiss", $username, $hashedPassword, $authToken, $fullName, $birthYear, $gender, $latitude, $longitude, $email, $profilePicturePath);
        return $query->execute();
    }

    public function activateAccount($authToken)
    {
        $query = $this->database->prepare("UPDATE user SET authToken = '' WHERE authToken = ?");
        $query->bind_param("s", $authToken);
        $query->execute();

        return $query->affected_rows > 0;
    }

    public function suggestQuestion($question, $username)
    {
        $query = $this->database->prepare("INSERT INTO sugerencia (contenido, username, estado) VALUES (?, ?, 0)");
        $query->bind_param("ss", $question, $username);
        return $query->execute();
    }

    public function getScore($username)
    {
        $query = $this->database->prepare("SELECT SUM(puntuacion) AS puntuacion_total FROM partida WHERE username = ?");
        $query->bind_param("s", $username);
        $query->execute();
        $query->bind_result($puntuacion_total);
        $query->fetch();
        return $puntuacion_total;
    }

    private function endAllPartidas($username)
    {
        $query = $this->database->prepare("UPDATE partida SET finalizada = 1 WHERE username = ? AND finalizada = 0");
        $query->bind_param("s", $username);
        $query->execute();
    }

}
