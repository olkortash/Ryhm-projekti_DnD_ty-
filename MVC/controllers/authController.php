<?php
require_once __DIR__ . '/../database/models/users.php';
require_once __DIR__ . '/../database/models/campaign.php';

class AuthController {
    private $userModel;
    private $campaignModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
        $this->campaignModel = new Campaign($pdo);
    }

    public function landing() {
        $campaigns = $this->campaignModel->getPublicCampaigns();
        require __DIR__ . '/../views/mainpage.php';
    }

    public function login() {
        if (isset($_GET['timeout']) && $_GET['timeout'] === '1') {
            $error = "Are you still there? Please log in again.";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = (string)($_POST['password'] ?? '');

            if ($username === '' || mb_strlen($username) > 50 || $password === '') {
                $error = 'Enter a valid username and password.';
                require __DIR__ . '/../views/login.php';
                return;
            }

            $user = $this->userModel->login($username, $password);
            if ($user) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['last_activity'] = time();
                header('Location: index.php?action=dashboard');
                exit;
            } else {
                $error = "Invalid username or password.";
                require __DIR__ . '/../views/login.php';
            }
        } else {
            require __DIR__ . '/../views/login.php';
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = (string)($_POST['password'] ?? '');
            $passwordConfirm = (string)($_POST['password_confirm'] ?? '');

            if ($username === '' || mb_strlen($username) > 50
                || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 75
                || strlen($password) < 8 || strlen($password) > 255
                || $password !== $passwordConfirm) {
                $error = 'Check the username, email address, and matching password of at least 8 characters.';
                require __DIR__ . '/../views/register.php';
                return;
            }

            if ($this->userModel->register($username, $email, $password)) {
                $user = $this->userModel->login($username, $password);

                if ($user) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['last_activity'] = time();
                }

                header('Location: index.php?action=dashboard');
                exit;
            } else {
                $error = "Registration failed.";
                require __DIR__ . '/../views/register.php';
            }
        } else {
            require __DIR__ . '/../views/register.php';
        }
    }

    public function logout() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=landing');
            exit;
        }
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 3600,
                $params['path'],
                $params['domain'] ?? '',
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        header('Location: index.php?action=landing');
        exit;
    }
}
