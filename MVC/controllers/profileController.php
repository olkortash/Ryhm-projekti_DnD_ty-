<?php
require_once __DIR__ . '/../database/models/users.php';

class ProfileController {
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $user = $this->userModel->getById($_SESSION['user_id']);

        if (!$user) {
            session_destroy();
            header('Location: index.php?action=login');
            exit;
        }

        require __DIR__ . '/../views/profile.php';
    }
}