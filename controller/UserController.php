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

    public function suggestView()
    {
        $data = [];
        $this->presenter->render("view/player/suggestQuestionView.mustache", $data);
    }

    public function suggest()
    {
        $question = $_POST['question'];
        $username = $_SESSION['user']['username'];
        $data = [];

        if($this->model->suggestQuestion($question, $username)) {
            $data['success'] = 'Pregunta enviada exitosamente.';
        } else {
            $data['error'] = 'Error al enviar la pregunta.';
        }
        $this->presenter->render("view/player/suggestQuestionView.mustache", $data);
    }

    public function reportQuestion()
    {
        $questionId = $_GET['questionId'];
        $username = $_SESSION['user']['username'];
        $data = [];

        if($this->model->reportQuestion($questionId, $username)) {
            header('Location: /partida/get');
            exit();
        }
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
        $username = $_SESSION['user']['username'];
        $user = $this->model->getUserByUsername($username);
        $_SESSION['user'] = $user;

        $puntaje_total = $this->getScore();

        $data = [
            'full_name' => $user['full_name'],
            'birth_year' => $user['birth_year'],
            'gender' => $user['gender'],
            'latitude' => $user['latitude'],
            'longitude' => $user['longitude'],
            'email' => $user['email'],
            'username' => $user['username'],
            'profile_picture' => $user['profile_picture'],
            'puntaje_total' => $puntaje_total
        ];

        $this->presenter->render("view/player/lobbyView.mustache", $data);
    }

    private function getScore()
    {
        $username = $_SESSION['user']['username'];
        return $this->model->getScore($username);
    }

    public function profile()
    {
        if (isset($_GET['username'])) {
            $username = $_GET['username'];
            $user = $this->model->getUserByUsername($username);
        } else {
            $user = $_SESSION['user'];
        }

        $profileLink = "http://localhost/user/profile/username=" . $user['username'];
        $qrCodeFile = 'public/qrcodes/' . $user['username'] . '.png';
        $this->generateQRCode($profileLink, $qrCodeFile);

        $data = [
            'full_name' => $user['full_name'],
            'birth_year' => $user['birth_year'],
            'gender' => $user['gender'],
            'latitude' => $user['latitude'],
            'longitude' => $user['longitude'],
            'email' => $user['email'],
            'username' => $user['username'],
            'profile_picture' => $user['profile_picture'],
            'qrCodeFile' => $qrCodeFile,
            'apiKey' => $this->apiKey,
        ];
        $this->presenter->render("view/player/profileView.mustache", $data);
    }

    private function generateQRCode($text, $file)
    {
        require_once('vendor/phpqrcode/qrlib.php');

        QRcode::png($text, $file, QR_ECLEVEL_L, 10);
    }

    public function errorView()
    {
        $data = [];
        $this->presenter->render("view/template/accessDeniedView.mustache", $data);
    }

    public function login()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $data = [];
        $role = '';

        $user = $this->model->login($username, $password);

        if ($user !== null) {
            $_SESSION['user'] = $user;

            switch($_SESSION['user']['role']) {
                case 'a':
                    $role = 'admin';
                    break;
                case 'e':
                    $role = 'editor';
                    break;
                case 'u':
                    $role = 'user';
                    break;
            }

            header('Location: /' . $role);
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
        $targetDir = "public/profile_pictures/";

        $uniqueName = uniqid() . "." . pathinfo($file["name"], PATHINFO_EXTENSION);
        $targetFile = $targetDir . $uniqueName;

        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return "/" . $targetFile;
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
