<?php

$sessionTimeoutSeconds = 3600;
ini_set('session.gc_maxlifetime', (string) $sessionTimeoutSeconds);
ini_set('session.cookie_lifetime', '0');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax',
]);

session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_token(): string {
    return (string)($_SESSION['csrf_token'] ?? '');
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

if (isset($_SESSION['user_id'])) {
    $now = time();

    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = $now;
    }

    if (($now - $_SESSION['last_activity']) > $sessionTimeoutSeconds) {
        session_unset();
        session_destroy();

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        header('Location: index.php?action=login&timeout=1');
        exit;
    }

    $_SESSION['last_activity'] = $now;
}

require_once __DIR__ . '/../database/connection.php';

$pdo = connectDB();

$action = $_GET['action'] ?? 'landing';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = $_POST['csrf_token'] ?? '';
    if (!is_string($postedToken) || csrf_token() === '' || !hash_equals(csrf_token(), $postedToken)) {
        http_response_code(403);
        exit('The request could not be verified. Please return to the previous page and try again.');
    }
}

switch ($action) {
    case 'search':
        require_once __DIR__ . '/../controllers/searchController.php';
        (new SearchController($pdo))->index();
        break;
    case 'landing':
        require_once __DIR__ . '/../controllers/authController.php';
        (new AuthController($pdo))->landing();
        break;
    case 'help':
        require __DIR__ . '/../views/help.php';
        break;
    case 'sitemap':
        require __DIR__ . '/../views/sitemap.php';
        break;
    case 'login':
        require_once __DIR__ . '/../controllers/authController.php';
        (new AuthController($pdo))->login();
        break;
    case 'register':
        require_once __DIR__ . '/../controllers/authController.php';
        (new AuthController($pdo))->register();
        break;
    case 'logout':
        require_once __DIR__ . '/../controllers/authController.php';
        (new AuthController($pdo))->logout();
        break;
    case 'dashboard':
        require_once __DIR__ . '/../controllers/dashboardController.php';
        (new DashboardController($pdo))->index();
        break;
    case 'profile':
        require_once __DIR__ . '/../controllers/profileController.php';
        (new ProfileController($pdo))->index();
        break;
    case 'character_create':
        require_once __DIR__ . '/../controllers/characterController.php';
        (new CharacterController($pdo))->create();
        break;
    case 'character_view':
        require_once __DIR__ . '/../controllers/characterController.php';
        (new CharacterController($pdo))->view();
        break;
    case 'character_image':
        require_once __DIR__ . '/../controllers/characterController.php';
        (new CharacterController($pdo))->image();
        break;
    case 'character_update_image':
        require_once __DIR__ . '/../controllers/characterController.php';
        (new CharacterController($pdo))->updateImage();
        break;
    case 'character_update_hp':
        require_once __DIR__ . '/../controllers/characterController.php';
        (new CharacterController($pdo))->updateHp();
        break;
    case 'character_update_abilities':
        require_once __DIR__ . '/../controllers/characterController.php';
        (new CharacterController($pdo))->updateAbilities();
        break;
    case 'character_update_details':
        require_once __DIR__ . '/../controllers/characterController.php';
        (new CharacterController($pdo))->updateAdditionalInfo();
        break;
    case 'character_delete':
        require_once __DIR__ . '/../controllers/characterController.php';
        (new CharacterController($pdo))->delete();
        break;
    case 'character_join_campaign':
        require_once __DIR__ . '/../controllers/characterController.php';
        (new CharacterController($pdo))->joinCampaign();
        break;
    case 'character_unlink_campaign':
        require_once __DIR__ . '/../controllers/characterController.php';
        (new CharacterController($pdo))->unlinkCampaign();
        break;
    case 'campaign_remove_character':
        require_once __DIR__ . '/../controllers/characterController.php';
        (new CharacterController($pdo))->removeFromCampaign();
        break;
    case 'campaign_create':
        require_once __DIR__ . '/../controllers/campaignController.php';
        (new CampaignController($pdo))->create();
        break;
    case 'campaign_view':
        require_once __DIR__ . '/../controllers/campaignController.php';
        (new CampaignController($pdo))->view();
        break;
    case 'campaign_join_public':
        require_once __DIR__ . '/../controllers/campaignController.php';
        (new CampaignController($pdo))->joinPublic();
        break;
    case 'campaign_update':
        require_once __DIR__ . '/../controllers/campaignController.php';
        (new CampaignController($pdo))->update();
        break;
    case 'campaign_delete':
        require_once __DIR__ . '/../controllers/campaignController.php';
        (new CampaignController($pdo))->delete();
        break;        
    case 'campaign_session_save':
        require_once __DIR__ . '/../controllers/campaignController.php';
        (new CampaignController($pdo))->saveSession();
        break;
    case 'campaign_level_up':
        require_once __DIR__ . '/../controllers/campaignController.php';
        (new CampaignController($pdo))->levelUpCharacter();
        break;
    case 'campaign_update_hp':
        require_once __DIR__ . '/../controllers/campaignController.php';
        (new CampaignController($pdo))->updateCharacterHp();
        break;
    case 'campaign_members_update':
        require_once __DIR__ . '/../controllers/campaignController.php';
        (new CampaignController($pdo))->updateMembers();
        break;
    case 'campaign_announcement_save':
        require_once __DIR__ . '/../controllers/campaignController.php';
        (new CampaignController($pdo))->createAnnouncement();
        break;
    case 'campaign_announcement_delete':
        require_once __DIR__ . '/../controllers/campaignController.php';
        (new CampaignController($pdo))->deleteAnnouncement();
        break;
    case 'notifications':
        require_once __DIR__ . '/../controllers/campaignController.php';
        (new CampaignController($pdo))->notifications();
        break;
    case 'notification_read':
        require_once __DIR__ . '/../controllers/campaignController.php';
        (new CampaignController($pdo))->markNotificationRead();
        break;
    case 'notifications_read_all':
        require_once __DIR__ . '/../controllers/campaignController.php';
        (new CampaignController($pdo))->markAllNotificationsRead();
        break;
    default:
        require '../views/404_view.html';
        break;
}
