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
                'campaign_id' => null,
                'character_name' => trim($_POST['character_name']),
                'character_class_id' => $_POST['character_class_id'],
                'character_race_id' => $_POST['character_race_id'],
                'character_job_id' => $_POST['character_job_id'],
                'level' => $_POST['level'] ?? 1,
                'hp_max' => $_POST['hp_max'],
                'agi' => $_POST['agi'],
                'str' => $_POST['str'],
                'dex' => $_POST['dex'],
                'wis' => $_POST['wis'],
                'cha' => $_POST['cha'],
                'con' => $_POST['con'],
                'int' => $_POST['int']
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

        $isOwner = (int) $character['player_id'] === (int) $_SESSION['user_id'];
        require __DIR__ . '/../views/character_view.php';
    }

    public function image() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            exit;
        }

        $characterId = (int) ($_GET['id'] ?? 0);
        $image = $this->characterModel->getImageByCharacterId($characterId);

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
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $characterId = (int) ($_POST['character_id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hp_current']) && is_numeric($_POST['hp_current'])) {
            $this->characterModel->updateHp($characterId, (int) $_SESSION['user_id'], (int) $_POST['hp_current']);
        }

        header('Location: index.php?action=character_view&id=' . $characterId);
        exit;
    }

    public function updateAbilities() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=dashboard');
            exit;
        }

        $characterId = (int) ($_POST['character_id'] ?? 0);
        $abilityMap = [
            'agi' => 'agility',
            'str' => 'strength',
            'dex' => 'dexterity',
            'wis' => 'wisdom',
            'cha' => 'charisma',
            'con' => 'constitution',
            'int' => 'intelligence',
        ];

        $updates = [];
        foreach ($abilityMap as $input => $column) {
            $value = $_POST[$input] ?? null;
            if ($value === null || !is_numeric($value)) {
                continue;
            }
            $updates[$column] = max(0, min((int) $value, 99));
        }

        if ($updates !== []) {
            $this->characterModel->updateAbilities($characterId, (int) $_SESSION['user_id'], $updates);
        }

        header('Location: index.php?action=character_view&id=' . $characterId);
        exit;
    }

    public function updateAdditionalInfo() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=dashboard');
            exit;
        }

        $characterId = (int) ($_POST['character_id'] ?? 0);
        $equipment = trim((string) ($_POST['equipment'] ?? ''));
        $skills = trim((string) ($_POST['skills'] ?? ''));

        $this->characterModel->updateAdditionalInfo($characterId, (int) $_SESSION['user_id'], $equipment, $skills);

        header('Location: index.php?action=character_view&id=' . $characterId);
        exit;
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
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inviteCode = trim($_POST['invite_code'] ?? '');
            $characterId = (int) ($_POST['character_id'] ?? 0);

            $campaign = $this->campaignModel->getByInviteCode($inviteCode);
            if ($campaign && $this->characterModel->joinCampaign(
                $characterId,
                (int) $_SESSION['user_id'],
                (int) $campaign['campaign_id']
            )) {
                $this->campaignModel->createNotification(
                    $campaign['gm_id'],
                    $campaign['campaign_id'],
                    'campaign_member_joined',
                    'New player joined the campaign',
                    'A player joined your campaign with an invite code.',
                    [],
                    $_SESSION['user_id']
                );
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
            $character = $this->characterModel->getById($characterId);
            $removed = $this->characterModel->unlinkFromCampaignByGm(
                $characterId,
                $campaignId,
                $_SESSION['user_id']
            );
            if ($removed && $character && (int)$character['campaign_id'] === (int)$campaignId) {
                $this->campaignModel->createNotification(
                    $character['player_id'],
                    $campaignId,
                    'campaign_member_removed',
                    'Your character was removed from a campaign',
                    'The Game Master removed your character from the campaign.',
                    [],
                    $_SESSION['user_id']
                );
            }
        }

        header('Location: index.php?action=campaign_view&id=' . urlencode($campaignId));
        exit;
    }
}
