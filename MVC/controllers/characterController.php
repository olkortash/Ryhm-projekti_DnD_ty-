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

    private function canViewCharacter(array $character, int $userId): bool {
        return (int)$character['player_id'] === $userId
            || (!empty($character['campaign_id'])
                && $this->campaignModel->isCampaignMember((int)$character['campaign_id'], $userId));
    }

    public function create() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim((string)($_POST['character_name'] ?? ''));
            $integerFields = ['character_class_id', 'character_race_id', 'character_job_id', 'hp_max', 'agi', 'str', 'dex', 'wis', 'cha', 'con', 'int'];
            $values = [];
            foreach ($integerFields as $field) {
                $values[$field] = filter_var($_POST[$field] ?? null, FILTER_VALIDATE_INT);
            }
            $abilities = array_intersect_key($values, array_flip(['agi', 'str', 'dex', 'wis', 'cha', 'con', 'int']));
            $invalid = $name === '' || mb_strlen($name) > 100
                || in_array(false, $values, true)
                || $values['character_class_id'] < 1 || $values['character_race_id'] < 1 || $values['character_job_id'] < 1
                || $values['hp_max'] < 15 || $values['hp_max'] > 25
                || array_filter($abilities, static fn($score) => $score < 1 || $score > 25) !== []
                || array_sum($abilities) + $values['hp_max'] > 47;
            if ($invalid) {
                $error = 'Check the character name and use the available 47 points within the allowed stat ranges.';
            }

            $image = $this->readUploadedImage();

            if ($image['error'] !== null) {
                $error = $image['error'];
            }

            $data = [
                'player_id' => $_SESSION['user_id'],
                'campaign_id' => null,
                'character_name' => $name,
                'character_class_id' => $values['character_class_id'],
                'character_race_id' => $values['character_race_id'],
                'character_job_id' => $values['character_job_id'],
                'level' => 1,
                'hp_max' => $values['hp_max'],
                'agi' => $values['agi'], 'str' => $values['str'], 'dex' => $values['dex'],
                'wis' => $values['wis'], 'cha' => $values['cha'], 'con' => $values['con'], 'int' => $values['int']
            ];

            if (!$invalid && $image['error'] === null && $this->characterModel->create($data, $image['file'])) {
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

        if (!$this->canViewCharacter($character, (int)$_SESSION['user_id'])) {
            http_response_code(403);
            exit('Forbidden');
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
        $character = $this->characterModel->getById($characterId);
        if (!$character || !$this->canViewCharacter($character, (int)$_SESSION['user_id'])) {
            http_response_code(404);
            exit;
        }
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

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=dashboard'); exit;
        }
        $characterId = (int) ($_POST['character_id'] ?? 0);
        $character = $this->characterModel->getById($characterId);
        if (!$character || (int)$character['player_id'] !== (int)$_SESSION['user_id']) {
            http_response_code(403); exit('Forbidden');
        }
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

        $characterId = filter_var($_POST['character_id'] ?? null, FILTER_VALIDATE_INT);
        $hp = filter_var($_POST['hp_current'] ?? null, FILTER_VALIDATE_INT);
        $character = $characterId ? $this->characterModel->getById($characterId) : false;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $character && (int)$character['player_id'] === (int)$_SESSION['user_id']
            && $hp !== false && $hp >= 0 && $hp <= (int)$character['hp_max']) {
            $this->characterModel->updateHp($characterId, (int) $_SESSION['user_id'], $hp);
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
            if (!is_string($value) || filter_var($value, FILTER_VALIDATE_INT) === false) {
                header('Location: index.php?action=character_view&id=' . $characterId . '&error=abilities');
                exit;
            }
            $updates[$column] = (int) $value;
        }

        $character = $this->characterModel->getById($characterId);
        $hasInvalidScore = count($updates) !== count($abilityMap)
            || array_filter($updates, static fn($value) => $value < 1 || $value > 25) !== [];
        $exceedsPointBudget = !$character
            || array_sum($updates) + (int) $character['hp_max'] > 47;

        if (!$hasInvalidScore && !$exceedsPointBudget) {
            $this->characterModel->updateAbilities($characterId, (int) $_SESSION['user_id'], $updates);
        } else {
            header('Location: index.php?action=character_view&id=' . $characterId . '&error=abilities');
            exit;
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

        if (mb_strlen($equipment) > 5000 || mb_strlen($skills) > 5000) {
            header('Location: index.php?action=character_view&id=' . $characterId . '&error=details'); exit;
        }

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
