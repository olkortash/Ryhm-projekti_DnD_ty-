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
            $image = $this->readUploadedImage();

            if ($image['error'] !== null) {
                $error = $image['error'];
            }

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

            if ($image['error'] === null && $this->characterModel->create($data, $image['file'])) {
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

    public function image() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            exit;
        }

        $characterId = (int) ($_GET['id'] ?? 0);
        $image = $this->characterModel->getImageByCharacterId(
            $characterId,
            (int) $_SESSION['user_id']
        );

        if (!$image) {
            http_response_code(404);
            exit;
        }

        header('Content-Type: ' . $image['mime_type']);
        header('Content-Length: ' . strlen($image['image_data']));
        header('Cache-Control: private, max-age=3600');
        echo $image['image_data'];
        exit;
    }

    public function updateImage() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $characterId = (int) ($_POST['character_id'] ?? 0);
        $image = $this->readUploadedImage();
        $removeImage = isset($_POST['remove_image']);

        if ($image['error'] !== null && !$removeImage) {
            header('Location: index.php?action=character_view&id=' . $characterId . '&error=image');
            exit;
        }

        if ($image['file'] === null && !$removeImage) {
            header('Location: index.php?action=character_view&id=' . $characterId);
            exit;
        }

        $this->characterModel->updateImage(
            $characterId,
            (int) $_SESSION['user_id'],
            $removeImage ? null : $image['file']
        );

        header('Location: index.php?action=character_view&id=' . $characterId);
        exit;
    }

    private function readUploadedImage() {
        if (!isset($_FILES['character_image']) || $_FILES['character_image']['error'] === UPLOAD_ERR_NO_FILE) {
            return ['file' => null, 'error' => null];
        }

        $upload = $_FILES['character_image'];
        $maxBytes = 5 * 1024 * 1024;

        if ($upload['error'] !== UPLOAD_ERR_OK || $upload['size'] > $maxBytes) {
            return ['file' => null, 'error' => 'Please choose an image up to 5 MB in size.'];
        }

        $imageInfo = @getimagesize($upload['tmp_name']);
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        if (!$imageInfo || !in_array($imageInfo['mime'], $allowedMimeTypes, true)) {
            return ['file' => null, 'error' => 'Please upload a JPG, PNG, WEBP, or GIF image.'];
        }

        return [
            'file' => [
                'data' => file_get_contents($upload['tmp_name']),
                'mime_type' => $imageInfo['mime'],
            ],
            'error' => null,
        ];
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

    public function unlinkCampaign() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $characterId = $_POST['character_id'] ?? null;
            $this->characterModel->unlinkFromCampaignByOwner($characterId, $_SESSION['user_id']);
        }

        header('Location: index.php?action=dashboard');
        exit;
    }

    public function removeFromCampaign() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $campaignId = $_POST['campaign_id'] ?? null;
        $characterId = $_POST['character_id'] ?? null;
        $campaign = $this->campaignModel->getById($campaignId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $campaign
            && (int) $campaign['gm_id'] === (int) $_SESSION['user_id']) {
            $this->characterModel->unlinkFromCampaignByGm(
                $characterId,
                $campaignId,
                $_SESSION['user_id']
            );
        }

        header('Location: index.php?action=campaign_view&id=' . urlencode($campaignId));
        exit;
    }
}