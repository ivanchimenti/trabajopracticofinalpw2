<?php

class UserController
{
    private $presenter;
    private $model;

    public function __construct($presenter, $model)
    {
        $this->presenter = $presenter;
        $this->model = $model;
    }

    public function get()
    {
        $data = [];
        $this->presenter->render("view/loginView.mustache", $data);
    }

    public function registerView()
    {
        $data = [];
        $this->presenter->render("view/registerView.mustache", $data);
    }

    public function login()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $data = [];

        if ($this->model->login($username, $password)) {
            $_SESSION['username'] = $username;
            header('Location: /user/home');
            exit();
        } else {
            $data['error'] = 'Usuario o contraseña incorrectos';
            $this->presenter->render("view/loginView.mustache", $data);
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: /user');
        exit();
    }

    public function home()
    {
        $data = [];
        $this->presenter->render("view/lobbyView.mustache", $data);
    }

    public function register()
    {

        $fullName = $_POST['full_name'];
        $birthYear = $_POST['birth_year'];
        $gender = $_POST['gender'];
        $country = $_POST['country'];
        $city = $_POST['city'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        $username = $_POST['username'];
        $profilePicture = $_FILES['profile_picture'];
        $data = [];

        if ($password != $confirmPassword) {
            $data['error'] = 'Las contraseñas no coinciden.';
            $this->presenter->render("view/registerView.mustache", $data);
            return;
        }

        $hashedPassword = $this->hashPassword($password);
        $authToken = $this->generateAuthToken();
        $profilePicturePath = $this->uploadProfilePicture($profilePicture);

        if ($this->model->register($username, $hashedPassword, $authToken, $fullName, $birthYear, $gender, $country, $city, $email, $profilePicturePath)) {
            $data['success'] = 'Usuario registrado exitosamente. Por favor, verifica tu correo electrónico para activar tu cuenta.';
        } else {
            $data['error'] = 'Error en el registro del usuario.';
        }
        $this->presenter->render("view/registerView.mustache", $data);
    }

    public function activate()
    {
        if (!isset($_GET['token'])) {
            $data = ['error' => 'Token de activación no proporcionado.'];
            $this->presenter->render("view/loginView.mustache", $data);
            return;
        }

        $authToken = $_GET['token'];
        $data = [];

        if ($this->model->activateAccount($authToken)) {
            $data['success'] = 'Cuenta activada exitosamente.';
        } else {
            $data['error'] = 'Token de activación inválido o la cuenta ya ha sido activada.';
        }
        $this->presenter->render("view/loginView.mustache", $data);

    }

    private function uploadProfilePicture($file)
    {
        $targetDir = "uploads/profile_pictures/";
        $targetFile = $targetDir . basename($file["name"]);
        move_uploaded_file($file["tmp_name"], $targetFile);
        return $targetFile;
    }

    private function generateAuthToken()
    {
        return md5(uniqid(rand(), true));
    }

    private function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
