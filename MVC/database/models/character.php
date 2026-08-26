<?php
require_once __DIR__ . '/../connection.php';

class Character {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getByPlayerId($player_id) {
        $sql = "SELECT c.*, cl.class_name, r.race_name, j.job_name, cmp.campaign_name 
                FROM characters c
                LEFT JOIN classes cl ON c.character_class_id = cl.class_id
                LEFT JOIN races r ON c.character_race_id = r.race_id
                LEFT JOIN jobs j ON c.character_job_id = j.job_id
                LEFT JOIN campaigns cmp ON c.campaign_id = cmp.campaign_id
                WHERE c.player_id = :player_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':player_id' => $player_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($character_id) {
        $sql = "SELECT c.*, cl.class_name, r.race_name, j.job_name, cmp.campaign_name 
                FROM characters c
                LEFT JOIN classes cl ON c.character_class_id = cl.class_id
                LEFT JOIN races r ON c.character_race_id = r.race_id
                LEFT JOIN jobs j ON c.character_job_id = j.job_id
                LEFT JOIN campaigns cmp ON c.campaign_id = cmp.campaign_id
                WHERE c.character_id = :character_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':character_id' => $character_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO characters (player_id, campaign_id, character_name, character_class_id, character_race_id, character_job_id, level, hp_current, hp_max)
                VALUES (:player_id, :campaign_id, :character_name, :character_class_id, :character_race_id, :character_job_id, :level, :hp_current, :hp_max)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':player_id' => $data['player_id'],
            ':campaign_id' => $data['campaign_id'] ?? null,
            ':character_name' => $data['character_name'],
            ':character_class_id' => $data['character_class_id'],
            ':character_race_id' => $data['character_race_id'],
            ':character_job_id' => $data['character_job_id'],
            ':level' => $data['level'] ?? 1,
            ':hp_current' => $data['hp_max'],
            ':hp_max' => $data['hp_max']
        ]);
    }

    public function updateHp($character_id, $hp_current) {
        $sql = "UPDATE characters SET hp_current = :hp_current WHERE character_id = :character_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':hp_current' => $hp_current,
            ':character_id' => $character_id
        ]);
    }

    public function updateDetails($character_id, $data) {
        $sql = "UPDATE characters 
                SET character_name = :character_name, level = :level, hp_max = :hp_max, hp_current = :hp_current
                WHERE character_id = :character_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':character_name' => $data['character_name'],
            ':level' => $data['level'],
            ':hp_max' => $data['hp_max'],
            ':hp_current' => $data['hp_current'],
            ':character_id' => $character_id
        ]);
    }

    public function joinCampaign($character_id, $campaign_id) {
        $sql = "UPDATE characters SET campaign_id = :campaign_id WHERE character_id = :character_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':campaign_id' => $campaign_id,
            ':character_id' => $character_id
        ]);
    }

    // Apufunktiot lomakkeiden alasvetovalikoille
    public function getClasses() {
        return $this->pdo->query("SELECT * FROM classes")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRaces() {
        return $this->pdo->query("SELECT * FROM races")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getJobs() {
        return $this->pdo->query("SELECT * FROM jobs")->fetchAll(PDO::FETCH_ASSOC);
    }
}