<?php
require_once __DIR__ . '/../database/models/campaign.php';

class CampaignController {
    private $campaignModel;

    public function __construct($pdo) {
        $this->campaignModel = new Campaign($pdo);
    }

    private function flash($type, $message) {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
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

        $isGm = (int)$campaign['gm_id'] === (int)$_SESSION['user_id'];
        $canViewPrivate = $isGm || $this->campaignModel->isCampaignMember($campaignId, $_SESSION['user_id']);
        $players = $canViewPrivate ? $this->campaignModel->getCharactersInCampaign($campaignId) : [];
        $campaignMembers = $canViewPrivate ? $this->campaignModel->getMembers($campaignId) : [];
        $availableUsers = $isGm ? $this->campaignModel->getAvailableUsers($campaignId) : [];
        $availableCharacters = $isGm ? $this->campaignModel->getAvailableCharacters($campaignId) : [];
        $alreadyJoined = $canViewPrivate;
        $joinableCharacters = $this->campaignModel->getAvailableCharactersForPlayer($campaignId, $_SESSION['user_id']);
        $sessionNotes = $canViewPrivate ? $this->campaignModel->getSessionNotes($campaignId) : [];
        $announcements = $canViewPrivate ? $this->campaignModel->getAnnouncements($campaignId) : [];
        $notificationCount = $this->campaignModel->getUnreadNotificationCount($_SESSION['user_id']);
        require __DIR__ . '/../views/campaign_view.php';
    }

    public function joinPublic() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=landing');
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $campaignId = (int)($_POST['campaign_id'] ?? 0);
        $characterId = (int)($_POST['character_id'] ?? 0);
        $this->campaignModel->joinPublicCampaign($campaignId, $_SESSION['user_id'], $characterId);

        header('Location: index.php?action=campaign_view&id=' . $campaignId);
        exit;
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

        $sessionId = $this->campaignModel->saveSessionNote(
            $campaignId,
            $_SESSION['user_id'],
            $sessionDate,
            $title,
            $summary,
            $attendees
        );

        if ($sessionId) {
            $this->flash('success', 'Session note saved and campaign members notified.');
        } else {
            $this->flash('error', 'Session note could not be saved.');
        }

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

    public function createAnnouncement() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $campaignId = (int)($_POST['campaign_id'] ?? 0);
        $title = trim((string)($_POST['announcement_title'] ?? ''));
        $body = trim((string)($_POST['announcement_body'] ?? ''));
        $announcementId = (int)($_POST['announcement_id'] ?? 0);

        if ($title === '' || $body === '' || mb_strlen($title) > 255) {
            $this->flash('error', 'Announcement title and message are required.');
        } elseif ($announcementId > 0) {
            $updated = $this->campaignModel->updateAnnouncement(
                $announcementId,
                $_SESSION['user_id'],
                $title,
                $body
            );
            $this->flash($updated ? 'success' : 'error', $updated ? 'Announcement updated.' : 'Announcement could not be updated.');
        } else {
            $created = $this->campaignModel->createAnnouncement(
                $campaignId,
                $_SESSION['user_id'],
                $title,
                $body
            );
            $this->flash($created ? 'success' : 'error', $created ? 'Announcement published.' : 'Announcement could not be published.');
        }

        header('Location: index.php?action=campaign_view&id=' . $campaignId);
        exit;
    }

    public function deleteAnnouncement() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $campaignId = (int)($_POST['campaign_id'] ?? 0);
        $deleted = $this->campaignModel->deleteAnnouncement(
            (int)($_POST['announcement_id'] ?? 0),
            $_SESSION['user_id']
        );
        $this->flash($deleted ? 'success' : 'error', $deleted ? 'Announcement deleted.' : 'Announcement could not be deleted.');
        header('Location: index.php?action=campaign_view&id=' . $campaignId);
        exit;
    }

    public function notifications() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $notifications = $this->campaignModel->getNotifications($_SESSION['user_id']);
        $notificationCount = $this->campaignModel->getUnreadNotificationCount($_SESSION['user_id']);
        require __DIR__ . '/../views/notifications.php';
    }

    public function markNotificationRead() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $this->campaignModel->markNotificationRead(
            (int)($_POST['notification_id'] ?? 0),
            $_SESSION['user_id']
        );
        header('Location: index.php?action=notifications');
        exit;
    }

    public function markAllNotificationsRead() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $this->campaignModel->markAllNotificationsRead($_SESSION['user_id']);
        $this->flash('success', 'All notifications marked as read.');
        header('Location: index.php?action=notifications');
        exit;
    }
}