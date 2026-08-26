<?php
require_once __DIR__ . '/../database/models/character.php';
require_once __DIR__ . '/../database/models/campaign.php';

class DashboardController {
    private $characterModel;
    private $campaignModel;

    public function __construct($pdo) {
        $this->characterModel = new Character($pdo);
        $this->campaignModel = new Campaign($pdo);
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $characters = $this->characterModel->getByPlayerId($userId);
        $gmCampaigns = $this->campaignModel->getByGmId($userId);

        require __DIR__ . '/../views/dashboard.php';
    }
}