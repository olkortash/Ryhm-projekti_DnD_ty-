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

        if (!$campaign) {
            header('Location: index.php?action=dashboard');
            exit;
        }

        $isGm = $campaign['gm_id'] == $_SESSION['user_id'];
        $players = $this->campaignModel->getCharactersInCampaign($campaignId);
        $campaignMembers = $this->campaignModel->getMembers($campaignId);
        $availableUsers = $this->campaignModel->getAvailableUsers($campaignId);
        $availableCharacters = $this->campaignModel->getAvailableCharacters($campaignId);
        $sessionNotes = $this->campaignModel->getSessionNotes($campaignId);
        require __DIR__ . '/../views/campaign_view.php';
    }

    public function updateMembers() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=dashboard');
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $campaignId = (int)($_POST['campaign_id'] ?? 0);
        $campaign = $this->campaignModel->getById($campaignId);
        if (!$campaign || (int)$campaign['gm_id'] !== (int)$_SESSION['user_id']) {
            header('Location: index.php?action=dashboard');
            exit;
        }

        if (isset($_POST['add_member'])) {
            $characterId = (int)($_POST['character_id'] ?? 0);
            $role = $_POST['member_role'] ?? 'Player';
            $this->campaignModel->addMember($campaignId, $_SESSION['user_id'], 0, $role, $characterId);
        }

        if (isset($_POST['update_member_role'])) {
            $userId = (int)($_POST['user_id'] ?? 0);
            $role = $_POST['member_role'] ?? 'Player';
            $this->campaignModel->setMemberRole($campaignId, $_SESSION['user_id'], $userId, $role);
        }

        if (isset($_POST['remove_member'])) {
            $userId = (int)($_POST['user_id'] ?? 0);
            $this->campaignModel->removeMember($campaignId, $_SESSION['user_id'], $userId);
        }

        header('Location: index.php?action=campaign_view&id=' . $campaignId);
        exit;
    }

    public function saveSession() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=dashboard');
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $campaignId = (int)($_POST['campaign_id'] ?? 0);
        $sessionDate = trim($_POST['session_date'] ?? '');
        $title = trim($_POST['session_title'] ?? '');
        $summary = trim($_POST['session_summary'] ?? '');
        $attendees = $_POST['attendees'] ?? [];

        $campaign = $this->campaignModel->getById($campaignId);
        if (!$campaign || (int)$campaign['gm_id'] !== (int)$_SESSION['user_id']) {
            header('Location: index.php?action=dashboard');
            exit;
        }

        $this->campaignModel->saveSessionNote(
            $campaignId,
            $_SESSION['user_id'],
            $sessionDate,
            $title,
            $summary,
            $attendees
        );

        header('Location: index.php?action=campaign_view&id=' . $campaignId);
        exit;
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user_id'])) {
                header('Location: index.php?action=login');
                exit;
            }

            $campaignId = $_POST['campaign_id'];
            $name = trim($_POST['campaign_name']);
            $description = trim($_POST['description']);

            $this->campaignModel->update($campaignId, $_SESSION['user_id'], $name, $description);

            $redirect = $_GET['redirect'] ?? 'campaign_view';
            if ($redirect === 'dashboard') {
                header("Location: index.php?action=dashboard");
            } else {
                header("Location: index.php?action=campaign_view&id=" . $campaignId);
            }
            exit;
        }
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user_id'])) {
                header('Location: index.php?action=login');
                exit;
            }

            $campaignId = $_POST['campaign_id'];
            $this->campaignModel->delete($campaignId, $_SESSION['user_id']);

            $redirect = $_GET['redirect'] ?? 'dashboard';
            header("Location: index.php?action=" . $redirect);
            exit;
        }
    }
}