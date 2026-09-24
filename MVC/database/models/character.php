<?php
require_once __DIR__ . '/../connection.php';

class Character {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getByPlayerId($player_id) {
        $sql = "SELECT c.*, u.username AS creator_username, cl.class_name, r.race_name, j.job_name, cmp.campaign_name
                FROM characters c
                JOIN users u ON c.player_id = u.user_id
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
        $sql = "SELECT c.*, u.username AS creator_username, cl.class_name, r.race_name, j.job_name, cmp.campaign_name
                FROM characters c
                JOIN users u ON c.player_id = u.user_id
                LEFT JOIN classes cl ON c.character_class_id = cl.class_id
                LEFT JOIN races r ON c.character_race_id = r.race_id
                LEFT JOIN jobs j ON c.character_job_id = j.job_id
                LEFT JOIN campaigns cmp ON c.campaign_id = cmp.campaign_id
                WHERE c.character_id = :character_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':character_id' => $character_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data, $image = null) {
        $this->pdo->beginTransaction();

        try {
                    $sql = "INSERT INTO characters (player_id, campaign_id, character_name, character_class_id, character_race_id, character_job_id, level, hp_current, hp_max, agility, strength, dexterity, wisdom, charisma, constitution, intelligence)
                        VALUES (:player_id, :campaign_id, :character_name, :character_class_id, :character_race_id, :character_job_id, :level, :hp_current, :hp_max, :agi, :str, :dex, :wis, :cha, :con, :int)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':player_id' => $data['player_id'],
                ':campaign_id' => $data['campaign_id'] ?? null,
                ':character_name' => $data['character_name'],
                ':character_class_id' => $data['character_class_id'],
                ':character_race_id' => $data['character_race_id'],
                ':character_job_id' => $data['character_job_id'],
                ':level' => $data['level'] ?? 1,
                ':hp_current' => $data['hp_max'],
                ':hp_max' => $data['hp_max'],
                ':agi' => $data['agi'],
                ':str' => $data['str'],
                ':dex' => $data['dex'],
                ':wis' => $data['wis'],
                ':cha' => $data['cha'],
                ':con' => $data['con'],
                ':int' => $data['int']
            ]);

            if ($image) {
                $imageId = $this->insertImage($image);
                $update = $this->pdo->prepare(
                    'UPDATE characters SET character_img_id = :image_id WHERE character_id = :character_id'
                );
                $update->execute([
                    ':image_id' => $imageId,
                    ':character_id' => $this->pdo->lastInsertId(),
                ]);
            }

            $this->pdo->commit();
            return true;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function updateImage($character_id, $player_id, $image = null) {
        $oldImageId = null;
        $this->pdo->beginTransaction();

        try {
            $find = $this->pdo->prepare(
                'SELECT character_img_id FROM characters WHERE character_id = :character_id AND player_id = :player_id FOR UPDATE'
            );
            $find->execute([
                ':character_id' => $character_id,
                ':player_id' => $player_id,
            ]);
            $character = $find->fetch(PDO::FETCH_ASSOC);

            if (!$character) {
                $this->pdo->rollBack();
                return false;
            }

            $oldImageId = $character['character_img_id'];
            $newImageId = $image ? $this->insertImage($image) : null;
            $update = $this->pdo->prepare(
                'UPDATE characters SET character_img_id = :image_id WHERE character_id = :character_id AND player_id = :player_id'
            );
            $update->execute([
                ':image_id' => $newImageId,
                ':character_id' => $character_id,
                ':player_id' => $player_id,
            ]);

            if ($oldImageId) {
                $this->deleteImage($oldImageId);
            }

            $this->pdo->commit();
            return true;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function getImageByCharacterId($character_id) {
        $sql = 'SELECT ci.image_data, ci.mime_type
                FROM character_images ci
                INNER JOIN characters c ON c.character_img_id = ci.image_id
                WHERE c.character_id = :character_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':character_id' => $character_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function insertImage($image) {
        $stmt = $this->pdo->prepare(
            'INSERT INTO character_images (image_data, mime_type) VALUES (:image_data, :mime_type)'
        );
        $stmt->bindValue(':image_data', $image['data'], PDO::PARAM_LOB);
        $stmt->bindValue(':mime_type', $image['mime_type'], PDO::PARAM_STR);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    private function deleteImage($image_id) {
        $stmt = $this->pdo->prepare('DELETE FROM character_images WHERE image_id = :image_id');
        $stmt->execute([':image_id' => $image_id]);
    }

    public function updateHp($character_id, $player_id, $hp_current) {
        $sql = "UPDATE characters
                SET hp_current = LEAST(GREATEST(:hp_current, 0), hp_max)
                WHERE character_id = :character_id AND player_id = :player_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':hp_current' => $hp_current,
            ':character_id' => $character_id,
            ':player_id' => $player_id
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

    public function delete($character_id, $player_id) {
        $sql = "DELETE FROM characters WHERE character_id = :character_id AND player_id = :player_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':character_id' => $character_id,
            ':player_id' => $player_id
        ]);
    }

    public function joinCampaign($character_id, $player_id, $campaign_id) {
        $sql = "UPDATE characters SET campaign_id = :campaign_id
                WHERE character_id = :character_id
                  AND player_id = :player_id
                  AND campaign_id IS NULL";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            ':campaign_id' => $campaign_id,
            ':character_id' => $character_id,
            ':player_id' => $player_id
        ]);

        $characterWasJoined = $result && $stmt->rowCount() === 1;
        if ($characterWasJoined) {
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS campaign_members (
                    member_id INT AUTO_INCREMENT PRIMARY KEY,
                    campaign_id INT NOT NULL,
                    user_id INT NOT NULL,
                    role ENUM('Player', 'Game Master') NOT NULL DEFAULT 'Player',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_campaign_user (campaign_id, user_id),
                    KEY idx_campaign_user (campaign_id, user_id),
                    KEY idx_campaign_role (campaign_id, role),
                    FOREIGN KEY (campaign_id) REFERENCES campaigns(campaign_id) ON DELETE CASCADE,
                    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
                )
            ");

            $character = $this->getById($character_id);
            if ($character && !empty($character['player_id'])) {
                $memberSql = "INSERT INTO campaign_members (campaign_id, user_id, role)
                              VALUES (:campaign_id, :user_id, 'Player')
                              ON DUPLICATE KEY UPDATE role = VALUES(role)";
                $memberStmt = $this->pdo->prepare($memberSql);
                $memberStmt->execute([
                    ':campaign_id' => $campaign_id,
                    ':user_id' => $character['player_id'],
                ]);
            }
        }

        return $characterWasJoined;
    }

    public function unlinkFromCampaignByOwner($character_id, $player_id) {
        $this->pdo->beginTransaction();

        try {
            $find = $this->pdo->prepare(
                "SELECT campaign_id
                 FROM characters
                 WHERE character_id = :character_id
                   AND player_id = :player_id
                   AND campaign_id IS NOT NULL
                 LIMIT 1"
            );
            $find->execute([
                ':character_id' => $character_id,
                ':player_id' => $player_id,
            ]);
            $character = $find->fetch(PDO::FETCH_ASSOC);

            if (!$character) {
                $this->pdo->rollBack();
                return false;
            }

            $campaignId = (int)$character['campaign_id'];
            $unlink = $this->pdo->prepare(
                "UPDATE characters
                 SET campaign_id = NULL
                 WHERE character_id = :character_id
                   AND player_id = :player_id
                   AND campaign_id = :campaign_id"
            );
            $unlink->execute([
                ':character_id' => $character_id,
                ':player_id' => $player_id,
                ':campaign_id' => $campaignId,
            ]);

            $remaining = $this->pdo->prepare(
                "SELECT 1
                 FROM characters
                 WHERE player_id = :player_id AND campaign_id = :campaign_id
                 LIMIT 1"
            );
            $remaining->execute([
                ':player_id' => $player_id,
                ':campaign_id' => $campaignId,
            ]);

            if (!$remaining->fetch()) {
                $removeMember = $this->pdo->prepare(
                    "DELETE FROM campaign_members
                     WHERE campaign_id = :campaign_id
                       AND user_id = :user_id
                       AND NOT EXISTS (
                           SELECT 1
                           FROM campaigns
                           WHERE campaign_id = :owner_campaign_id AND gm_id = :owner_id
                       )"
                );
                $removeMember->execute([
                    ':campaign_id' => $campaignId,
                    ':user_id' => $player_id,
                    ':owner_campaign_id' => $campaignId,
                    ':owner_id' => $player_id,
                ]);
            }

            $this->pdo->commit();
            return true;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function unlinkFromCampaignByGm($character_id, $campaign_id, $gm_id) {
        $sql = "UPDATE characters
                SET campaign_id = NULL
                WHERE character_id = :character_id
                  AND campaign_id = :campaign_id_filter
                  AND EXISTS (
                      SELECT 1 FROM campaigns
                      WHERE campaign_id = :campaign_id_exists
                        AND gm_id = :gm_id
                  )";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':character_id' => $character_id,
            ':campaign_id_filter' => $campaign_id,
            ':campaign_id_exists' => $campaign_id,
            ':gm_id' => $gm_id
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
