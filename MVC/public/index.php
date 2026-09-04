<?php

session_start();

require_once __DIR__ . '/../database/connection.php';

$pdo = connectDB();

$action = $_GET['action'] ?? 'landing';

switch ($action) {
    case 'landing':
        require_once __DIR__ . '/../controllers/authController.php';
        (new AuthController($pdo))->landing();
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
    case 'character_update_hp':
        require_once __DIR__ . '/../controllers/characterController.php';
        (new CharacterController($pdo))->updateHp();
        break;
    case 'character_delete':
        require_once __DIR__ . '/../controllers/characterController.php';
        (new CharacterController($pdo))->delete();
        break;
    case 'character_join_campaign':
        require_once __DIR__ . '/../controllers/characterController.php';
        (new CharacterController($pdo))->joinCampaign();
        break;
    case 'campaign_create':
        require_once __DIR__ . '/../controllers/campaignController.php';
        (new CampaignController($pdo))->create();
        break;
    case 'campaign_view':
        require_once __DIR__ . '/../controllers/campaignController.php';
        (new CampaignController($pdo))->view();
        break;
    case 'campaign_update':
        require_once __DIR__ . '/../controllers/campaignController.php';
        (new CampaignController($pdo))->update();
        break;
    case 'campaign_delete':
        require_once __DIR__ . '/../controllers/campaignController.php';
        (new CampaignController($pdo))->delete();
        break;        
    default:
        echo "404 - Sivua ei löytynyt";
        break;
}