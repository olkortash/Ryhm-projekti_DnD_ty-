<?php
require_once __DIR__ . '/../connection.php';

class Campaign {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    private function ensureSessionTrackingTablesExist() {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS campaign_sessions (
                session_id INT AUTO_INCREMENT PRIMARY KEY,
                campaign_id INT NOT NULL,
                session_date DATE NOT NULL,
                title VARCHAR(255) NOT NULL,
                summary TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (campaign_id) REFERENCES campaigns(campaign_id) ON DELETE CASCADE
            )
        ");

        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS campaign_session_attendees (
                attendee_id INT AUTO_INCREMENT PRIMARY KEY,
                session_id INT NOT NULL,
                user_id INT NOT NULL,
                attended TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_session_user (session_id, user_id),
                FOREIGN KEY (session_id) REFERENCES campaign_sessions(session_id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
            )
        ");
    }

    private function ensureCampaignMemberTableExists() {
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
    }

    public function getMembers($campaign_id) {
        $this->ensureCampaignMemberTableExists();

        $sql = "SELECT cm.*, u.username, u.user_id
                FROM campaign_members cm
                JOIN users u ON u.user_id = cm.user_id
                WHERE cm.campaign_id = :campaign_id
                ORDER BY cm.role = 'Game Master' DESC, u.username ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':campaign_id' => $campaign_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableUsers($campaign_id) {
        $this->ensureCampaignMemberTableExists();

        $sql = "SELECT u.user_id, u.username
                FROM users u
                LEFT JOIN campaign_members cm ON cm.user_id = u.user_id AND cm.campaign_id = :campaign_id
                WHERE cm.member_id IS NULL
                ORDER BY u.username ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':campaign_id' => $campaign_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addMember($campaign_id, $gm_id, $user_id, $role) {
        $this->ensureCampaignMemberTableExists();

        $campaign = $this->getById($campaign_id);
        if (!$campaign || (int)$campaign['gm_id'] !== (int)$gm_id) {
            return false;
        }

        $user_id = (int)$user_id;
        $role = in_array($role, ['Player', 'Game Master'], true) ? $role : 'Player';

        if ($user_id <= 0 || $user_id === (int)$campaign['gm_id']) {
            return false;
        }

        $existing = $this->pdo->prepare("SELECT * FROM campaign_members WHERE campaign_id = :campaign_id AND user_id = :user_id LIMIT 1");
        $existing->execute([':campaign_id' => $campaign_id, ':user_id' => $user_id]);
        $existingRow = $existing->fetch(PDO::FETCH_ASSOC);

        if ($role === 'Game Master') {
            $gmExists = $this->pdo->prepare("SELECT user_id FROM campaign_members WHERE campaign_id = :campaign_id AND role = 'Game Master' AND user_id != :user_id LIMIT 1");
            $gmExists->execute([':campaign_id' => $campaign_id, ':user_id' => $user_id]);
            if ($gmExists->fetch()) {
                return false;
            }
        }

        if ($existingRow) {
            $updateSql = "UPDATE campaign_members SET role = :role WHERE campaign_id = :campaign_id AND user_id = :user_id";
            $updateStmt = $this->pdo->prepare($updateSql);
            return $updateStmt->execute([
                ':role' => $role,
                ':campaign_id' => $campaign_id,
                ':user_id' => $user_id,
            ]);
        }

        $insertSql = "INSERT INTO campaign_members (campaign_id, user_id, role) VALUES (:campaign_id, :user_id, :role)";
        $insertStmt = $this->pdo->prepare($insertSql);
        return $insertStmt->execute([
            ':campaign_id' => $campaign_id,
            ':user_id' => $user_id,
            ':role' => $role,
        ]);
    }

    public function setMemberRole($campaign_id, $gm_id, $user_id, $role) {
        $this->ensureCampaignMemberTableExists();

        $campaign = $this->getById($campaign_id);
        if (!$campaign || (int)$campaign['gm_id'] !== (int)$gm_id) {
            return false;
        }

        $user_id = (int)$user_id;
        $role = in_array($role, ['Player', 'Game Master'], true) ? $role : 'Player';

        if ($user_id <= 0 || $user_id === (int)$campaign['gm_id']) {
            return false;
        }

        if ($role === 'Game Master') {
            $existingGm = $this->pdo->prepare("SELECT user_id FROM campaign_members WHERE campaign_id = :campaign_id AND role = 'Game Master' AND user_id != :user_id LIMIT 1");
            $existingGm->execute([':campaign_id' => $campaign_id, ':user_id' => $user_id]);
            if ($existingGm->fetch()) {
                return false;
            }
        }

        $sql = "UPDATE campaign_members SET role = :role WHERE campaign_id = :campaign_id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':role' => $role,
            ':campaign_id' => $campaign_id,
            ':user_id' => $user_id,
        ]);
    }

    public function removeMember($campaign_id, $gm_id, $user_id) {
        $this->ensureCampaignMemberTableExists();

        $campaign = $this->getById($campaign_id);
        if (!$campaign || (int)$campaign['gm_id'] !== (int)$gm_id) {
            return false;
        }

        $user_id = (int)$user_id;
        if ($user_id <= 0 || $user_id === (int)$campaign['gm_id']) {
            return false;
        }

        $sql = "DELETE FROM campaign_members WHERE campaign_id = :campaign_id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':campaign_id' => $campaign_id,
            ':user_id' => $user_id,
        ]);
    }

    public function getSessionNotes($campaign_id) {
        $this->ensureSessionTrackingTablesExist();

        $sql = "SELECT cs.*,
                       GROUP_CONCAT(DISTINCT u.username ORDER BY u.username SEPARATOR ', ') AS attendee_names
                FROM campaign_sessions cs
                LEFT JOIN campaign_session_attendees csa ON csa.session_id = cs.session_id
                LEFT JOIN users u ON u.user_id = csa.user_id
                WHERE cs.campaign_id = :campaign_id
                GROUP BY cs.session_id
                ORDER BY cs.session_date DESC, cs.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':campaign_id' => $campaign_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveSessionNote($campaign_id, $gm_id, $sessionDate, $title, $summary, $attendeeUserIds) {
        $this->ensureSessionTrackingTablesExist();

        $campaign = $this->getById($campaign_id);
        if (!$campaign || (int)$campaign['gm_id'] !== (int)$gm_id) {
            return false;
        }

        $sessionDate = $sessionDate ?: date('Y-m-d');
        $title = trim((string)$title);
        if ($title === '') {
            $title = 'Session ' . date('d.m.Y', strtotime($sessionDate));
        }

        $sql = "INSERT INTO campaign_sessions (campaign_id, session_date, title, summary)
                VALUES (:campaign_id, :session_date, :title, :summary)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':campaign_id' => $campaign_id,
            ':session_date' => $sessionDate,
            ':title' => $title,
            ':summary' => $summary
        ]);

        $sessionId = $this->pdo->lastInsertId();
        $attendeeUserIds = array_map('intval', (array)$attendeeUserIds);
        $attendeeUserIds = array_unique(array_filter($attendeeUserIds, fn($id) => $id > 0));

        if (!empty($attendeeUserIds)) {
            $insertSql = "INSERT INTO campaign_session_attendees (session_id, user_id, attended)
                          VALUES (:session_id, :user_id, 1)";
            $insertStmt = $this->pdo->prepare($insertSql);

            foreach ($attendeeUserIds as $userId) {
                $insertStmt->execute([
                    ':session_id' => $sessionId,
                    ':user_id' => $userId,
                ]);
            }
        }

        return true;
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

        $campaignId = $this->pdo->lastInsertId();
        $this->ensureCampaignMemberTableExists();
        $memberSql = "INSERT INTO campaign_members (campaign_id, user_id, role) VALUES (:campaign_id, :user_id, 'Game Master')";
        $memberStmt = $this->pdo->prepare($memberSql);
        $memberStmt->execute([
            ':campaign_id' => $campaignId,
            ':user_id' => $gm_id,
        ]);

        return $campaignId;
    }

    public function update($campaign_id, $gm_id, $name, $description) {
        $sql = "UPDATE campaigns SET campaign_name = :campaign_name, description = :description WHERE campaign_id = :campaign_id AND gm_id = :gm_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':campaign_name' => $name,
            ':description' => $description,
            ':campaign_id' => $campaign_id,
            ':gm_id' => $gm_id
        ]);
    }

    public function delete($campaign_id, $gm_id) {
        $sql = "DELETE FROM campaigns WHERE campaign_id = :campaign_id AND gm_id = :gm_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':campaign_id' => $campaign_id,
            ':gm_id' => $gm_id
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