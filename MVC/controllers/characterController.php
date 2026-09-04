<?php
require_once __DIR__ . '/../database/models/character.php';
require_once __DIR__ . '/../database/models/campaign.php';

class CharacterController {
    private $characterModel;
    private $campaignModel;

    public function __construct($pdo) {
        $this->characterModel = new Character($pdo);
        $this->campaignModel = new Campaign($pdo);
    }

    public function create() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'player_id' => $_SESSION['user_id'],
                'campaign_id' => !empty($_POST['campaign_id']) ? $_POST['campaign_id'] : null,
                'character_name' => trim($_POST['character_name']),
                'character_class_id' => $_POST['character_class_id'],
                'character_race_id' => $_POST['character_race_id'],
                'character_job_id' => $_POST['character_job_id'],
                'level' => $_POST['level'] ?? 1,
                'hp_max' => $_POST['hp_max']
            ];

            if ($this->characterModel->create($data)) {
                header('Location: index.php?action=dashboard');
                exit;
            }
        }

        $classes = $this->characterModel->getClasses();
        $races = $this->characterModel->getRaces();
        $jobs = $this->characterModel->getJobs();
        require __DIR__ . '/../views/character_create.php';
    }

    public function view() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $characterId = $_GET['id'] ?? null;
        $character = $this->characterModel->getById($characterId);

        if (!$character) {
            header('Location: index.php?action=dashboard');
            exit;
        }

        require __DIR__ . '/../views/character_view.php';
    }

    public function updateHp() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $characterId = $_POST['character_id'];
            $newHp = $_POST['hp_current'];
            
            $this->characterModel->updateHp($characterId, $newHp);
            header("Location: index.php?action=character_view&id=" . $characterId);
            exit;
        }
    }

    public function delete() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $characterId = $_POST['character_id'] ?? null;
            $this->characterModel->delete($characterId, $_SESSION['user_id']);
        }

        header('Location: index.php?action=dashboard');
        exit;
    }

    public function joinCampaign() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inviteCode = trim($_POST['invite_code']);
            $characterId = $_POST['character_id'];

            $campaign = $this->campaignModel->getByInviteCode($inviteCode);
            if ($campaign) {
                $this->characterModel->joinCampaign($characterId, $campaign['campaign_id']);
                header("Location: index.php?action=character_view&id=" . $characterId);
                exit;
            } else {
                $error = "Virheellinen kutsukoodi.";
                header("Location: index.php?action=character_view&id=" . $characterId . "&error=invcode");
                exit;
            }
        }
    }
}