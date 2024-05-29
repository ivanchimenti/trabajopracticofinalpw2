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


    public function register($username, $password)
    {
        $hashedPassword = $this->hashPassword($password);
        $authToken = $this->generateAuthToken();
        $query = $this->database->prepare("INSERT INTO users (username, password, authToken) VALUES (?, ?, ?)");
        $query->bind_param("sss", $username, $hashedPassword, $authToken);
        $query->execute();
    }

    public function activateAccount($authToken)
    {
        $query = $this->database->prepare("UPDATE users SET authToken = '' WHERE authToken = ?");
        $query->bind_param("s", $authToken);
        $query->execute();

        return $query->affected_rows > 0;
    }

    public function generateAuthToken()
    {
        return md5(uniqid(rand(), true));
    }

    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
