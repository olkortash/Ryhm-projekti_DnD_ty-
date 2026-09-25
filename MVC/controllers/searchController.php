<?php
require_once __DIR__ . '/../database/models/users.php';
require_once __DIR__ . '/../database/models/campaign.php';

class SearchController {
    private $userModel;
    private $campaignModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
        $this->campaignModel = new Campaign($pdo);
    }

    public function index() {
        $input = $_GET['q'] ?? '';
        $searchQuery = is_string($input) ? trim($input) : '';
        $searchError = null;
        $users = [];
        $campaigns = [];

        // Tarkistus tehdään myös palvelimella: URL voi ohittaa kentän maxlength-rajan.
        if (!is_string($input) || !preg_match('/^.{0,100}$/us', $searchQuery)) {
            $searchQuery = '';
            $searchError = 'Enter a search term of up to 100 characters.';
        } elseif ($searchQuery !== '') {
            $users = $this->userModel->searchByUsername($searchQuery);
            $campaigns = $this->campaignModel->searchPublicCampaigns($searchQuery);
        }

        // Ylimääräinen osuma kertoo, että hakua kannattaa tarkentaa.
        $moreUsers = count($users) > 20;
        $moreCampaigns = count($campaigns) > 20;
        $users = array_slice($users, 0, 20);
        $campaigns = array_slice($campaigns, 0, 20);

        require __DIR__ . '/../views/search.php';
    }
}
