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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            $user = $this->userModel->login($username, $password);
            if ($user) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                header('Location: index.php?action=dashboard');
                exit;
            } else {
                $error = "Virheellinen käyttäjätunnus tai salasana.";
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
            $password = trim($_POST['password'] ?? '');

            if ($this->userModel->register($username, $email, $password)) {
                $user = $this->userModel->login($username, $password);

                if ($user) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                }

                header('Location: index.php?action=dashboard');
                exit;
            } else {
                $error = "Rekisteröinti epäonnistui.";
                require __DIR__ . '/../views/register.php';
            }
        } else {
            require __DIR__ . '/../views/register.php';
        }
    }

    public function logout() {
        session_destroy();
        header('Location: index.php?action=landing');
        exit;
    }
}