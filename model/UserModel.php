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
        $query = $this->database->prepare("INSERT INTO user (username, password, authToken, full_name, birth_year, gender, latitude, longitude, email, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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

    public function incrementarPuntuacion($username)
    {
        $query = $this->database->prepare("UPDATE user SET score = score + 1 WHERE username = ?");
        $query->bind_param('s', $username);
        return $query->execute();
    }
}
