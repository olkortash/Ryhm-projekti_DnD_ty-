<?php
require_once __DIR__ . '/../database/models/users.php';
require_once __DIR__ . '/../database/models/character.php';
require_once __DIR__ . '/../database/models/campaign.php';

class ProfileController {
    private $userModel;
    private $characterModel;
    private $campaignModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
        $this->characterModel = new Character($pdo);
        $this->campaignModel = new Campaign($pdo);
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

        $userId = $_SESSION['user_id'];
        $characters = $this->characterModel->getByPlayerId($userId);
        $createdCampaigns = $this->campaignModel->getByGmId($userId);
        $notificationCount = $this->campaignModel->getUnreadNotificationCount($userId);
        $joinedCampaignIds = [];

        foreach ($characters as $character) {
            if (!empty($character['campaign_id'])) {
                $joinedCampaignIds[$character['campaign_id']] = true;
            }
        }

        $characterCount = count($characters);
        $createdCampaignCount = count($createdCampaigns);
        $joinedCampaignCount = count($joinedCampaignIds);

        require __DIR__ . '/../views/profile.php';
    }
}