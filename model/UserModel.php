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
        $query = $this->database->prepare("SELECT password, authToken FROM users WHERE username = ?");
        $query->bind_param("s", $username);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password'];
            $authToken = $row['authToken'];

            if ($authToken != "") {
                return false;
            }

            if (password_verify($password, $hashedPassword)) {
                return true;
            }
        }
        return false;
    }


    public function register($username, $hashedPassword, $authToken, $fullName, $birthYear, $gender, $country, $city, $email, $profilePicturePath)
    {
        $query = $this->database->prepare("INSERT INTO users (username, password, authToken, full_name, birth_year, gender, country, city, email, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $query->bind_param("ssssisssss", $username, $hashedPassword, $authToken, $fullName, $birthYear, $gender, $country, $city, $email, $profilePicturePath);
        return $query->execute();
    }

    public function activateAccount($authToken)
    {
        $query = $this->database->prepare("UPDATE users SET authToken = '' WHERE authToken = ?");
        $query->bind_param("s", $authToken);
        $query->execute();

        return $query->affected_rows > 0;
    }

}
