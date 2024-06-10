<?php

require 'helper/sendEmail.php';

class UserController
{
    private $presenter;
    private $model;
    private $apiKey;

    public function __construct($presenter, $model, $apiKey)
    {
        $this->presenter = $presenter;
        $this->model = $model;
        $this->apiKey = $apiKey;
    }

    public function get()
    {
        $data = [];
        $this->presenter->render("view/loginView.mustache", $data);
    }

    public function registerView()
    {
        $data = [
            'apiKey' => $this->apiKey
        ];
        $this->presenter->render("view/registerView.mustache", $data);
    }

    public function lobby()
    {
        $user = $_SESSION['user'];
        $data = [
            'full_name' => $user['full_name'],
            'birth_year' => $user['birth_year'],
            'gender' => $user['gender'],
            'latitude' => $user['latitude'],
            'longitude' => $user['longitude'],
            'email' => $user['email'],
            'username' => $user['username'],
            'profile_picture' => $user['profile_picture']
        ];

        $this->presenter->render("view/lobbyView.mustache", $data);
    }

    public function profile()
    {
        if (isset($_GET['username'])) {
            $username = $_GET['username'];
            $user = $this->model->getUserByUsername($username);
        } else {
            $user = $_SESSION['user'];
        }

        $data = [
            'full_name' => $user['full_name'],
            'birth_year' => $user['birth_year'],
            'gender' => $user['gender'],
            'latitude' => $user['latitude'],
            'longitude' => $user['longitude'],
            'email' => $user['email'],
            'username' => $user['username'],
            'profile_picture' => $user['profile_picture'],
            'apiKey' => $this->apiKey
        ];
        $this->presenter->render("view/profileView.mustache", $data);
    }

    public function login()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $data = [];

        $user = $this->model->login($username, $password);

        if ($user !== null) {
            $_SESSION['user'] = $user;
            header('Location: /user/lobby');
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

    public function register()
    {
        $fullName = $_POST['full_name'];
        $birthYear = $_POST['birth_year'];
        $gender = $_POST['gender'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
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

        if ($this->model->register($username, $hashedPassword, $authToken, $fullName, $birthYear, $gender, $latitude, $longitude, $email, $profilePicturePath)) {
            $subject = "Verifica tu cuenta";
            $body = "Por favor, haz clic en el siguiente enlace para activar tu cuenta: <a href='http://localhost/user/activate?token=$authToken'>Activar Cuenta</a>";

            if (sendVerificationEmail($email, $fullName, $subject, $body)) {
                $data['success'] = 'Usuario registrado exitosamente. Por favor, verifica tu correo electrónico para activar tu cuenta.';
            } else {
                $data['error'] = 'Error al enviar el correo electrónico de verificación.';
            }
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

        $uniqueName = uniqid() . "." . pathinfo($file["name"], PATHINFO_EXTENSION);
        $targetFile = $targetDir . $uniqueName;

        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return $targetFile;
        } else {
            return false;
        }
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
