<?php

class UserModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function authenticate($username, $password)
    {
        $query = $this->database->prepare("SELECT password FROM users WHERE username = ?");
        $query->bind_param("s", $username);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password'];
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

    public function generateAuthToken()
    {
        return md5(uniqid(rand(), true));
    }

    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
