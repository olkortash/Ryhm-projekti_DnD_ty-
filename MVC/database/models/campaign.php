<?php
require_once __DIR__ . '/../connection.php';

class Campaign {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getByGmId($gm_id) {
        $sql = "SELECT * FROM campaigns WHERE gm_id = :gm_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':gm_id' => $gm_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPublicCampaigns() {
        $sql = "SELECT c.*, COUNT(ch.character_id) AS character_count
                FROM campaigns c
                LEFT JOIN characters ch ON ch.campaign_id = c.campaign_id
                GROUP BY c.campaign_id
                ORDER BY c.campaign_id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($campaign_id) {
        $sql = "SELECT * FROM campaigns WHERE campaign_id = :campaign_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':campaign_id' => $campaign_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByInviteCode($invite_code) {
        $sql = "SELECT * FROM campaigns WHERE invite_code = :invite_code";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':invite_code' => $invite_code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($gm_id, $name, $description) {
        $inviteCode = substr(md5(uniqid(rand(), true)), 0, 8); // Generoidaan max 8-merkkinen invite_code
        $sql = "INSERT INTO campaigns (gm_id, campaign_name, description, invite_code) VALUES (:gm_id, :campaign_name, :description, :invite_code)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':gm_id' => $gm_id,
            ':campaign_name' => $name,
            ':description' => $description,
            ':invite_code' => $inviteCode
        ]);
        return $this->pdo->lastInsertId();
    }

    public function update($campaign_id, $name, $description) {
        $sql = "UPDATE campaigns SET campaign_name = :campaign_name, description = :description WHERE campaign_id = :campaign_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':campaign_name' => $name,
            ':description' => $description,
            ':campaign_id' => $campaign_id
        ]);
    }

    public function getCharactersInCampaign($campaign_id) {
        $sql = "SELECT c.*, u.username as player_name, cl.class_name, r.race_name 
                FROM characters c
                JOIN users u ON c.player_id = u.user_id
                LEFT JOIN classes cl ON c.character_class_id = cl.class_id
                LEFT JOIN races r ON c.character_race_id = r.race_id
                WHERE c.campaign_id = :campaign_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':campaign_id' => $campaign_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}