<?php
require_once __DIR__ . '/../database/models/campaign.php';

class CampaignController {
    private $campaignModel;

    public function __construct($pdo) {
        $this->campaignModel = new Campaign($pdo);
    }

    public function create() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['campaign_name']);
            $description = trim($_POST['description']);
            $gmId = $_SESSION['user_id'];

            $campaignId = $this->campaignModel->create($gmId, $name, $description);
            header("Location: index.php?action=campaign_view&id=" . $campaignId);
            exit;
        }
    }

    public function view() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $campaignId = $_GET['id'] ?? null;
        $campaign = $this->campaignModel->getById($campaignId);

        if (!$campaign || $campaign['gm_id'] != $_SESSION['user_id']) {
            header('Location: index.php?action=dashboard');
            exit;
        }

        $players = $this->campaignModel->getCharactersInCampaign($campaignId);
        require __DIR__ . '/../views/campaign_view.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $campaignId = $_POST['campaign_id'];
            $name = trim($_POST['campaign_name']);
            $description = trim($_POST['description']);

            $this->campaignModel->update($campaignId, $name, $description);
            header("Location: index.php?action=campaign_view&id=" . $campaignId);
            exit;
        }
    }
}