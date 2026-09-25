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

        // Do not try to alter the table by adding another AUTO_INCREMENT column,
        // because MySQL rejects that schema and it is not required for this logic.
        $column = $this->pdo->query("SHOW COLUMNS FROM campaign_members LIKE 'member_id'")->fetch();
        if (!$column) {
            return;
        }
    }

    private function ensureCommunicationTablesExist() {
        $this->ensureSessionTrackingTablesExist();

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS campaign_announcements (
            announcement_id INT AUTO_INCREMENT PRIMARY KEY,
            campaign_id INT NOT NULL,
            author_user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            body TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_announcements_campaign (campaign_id, created_at),
            FOREIGN KEY (campaign_id) REFERENCES campaigns(campaign_id) ON DELETE CASCADE,
            FOREIGN KEY (author_user_id) REFERENCES users(user_id) ON DELETE CASCADE
        )");

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS notifications (
            notification_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            campaign_id INT NULL,
            type VARCHAR(64) NOT NULL,
            title VARCHAR(255) NOT NULL,
            body TEXT NOT NULL,
            announcement_id INT NULL,
            session_id INT NULL,
            actor_user_id INT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            read_at TIMESTAMP NULL DEFAULT NULL,
            KEY idx_notifications_user (user_id, read_at, created_at),
            KEY idx_notifications_campaign (campaign_id, created_at),
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
            FOREIGN KEY (campaign_id) REFERENCES campaigns(campaign_id) ON DELETE SET NULL,
            FOREIGN KEY (announcement_id) REFERENCES campaign_announcements(announcement_id) ON DELETE SET NULL,
            FOREIGN KEY (session_id) REFERENCES campaign_sessions(session_id) ON DELETE SET NULL,
            FOREIGN KEY (actor_user_id) REFERENCES users(user_id) ON DELETE SET NULL
        )");
    }

    public function isCampaignMember($campaign_id, $user_id) {
        $this->ensureCampaignMemberTableExists();

        $sql = "SELECT 1 FROM campaigns c
                LEFT JOIN campaign_members cm
                                        ON cm.campaign_id = c.campaign_id AND cm.user_id = :member_user_id
                WHERE c.campaign_id = :campaign_id
                                    AND (c.gm_id = :gm_user_id OR cm.user_id IS NOT NULL)
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':campaign_id' => (int)$campaign_id,
                        ':member_user_id' => (int)$user_id,
                        ':gm_user_id' => (int)$user_id,
        ]);
        return (bool)$stmt->fetchColumn();
    }

    public function getAnnouncements($campaign_id) {
        $this->ensureCommunicationTablesExist();

        $sql = "SELECT ca.*, u.username AS author_name
                FROM campaign_announcements ca
                JOIN users u ON u.user_id = ca.author_user_id
                WHERE ca.campaign_id = :campaign_id
                ORDER BY ca.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':campaign_id' => (int)$campaign_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createAnnouncement($campaign_id, $gm_id, $title, $body) {
        $campaign = $this->getById($campaign_id);
        if (!$campaign || (int)$campaign['gm_id'] !== (int)$gm_id) {
            return false;
        }

        $this->ensureCommunicationTablesExist();
        $stmt = $this->pdo->prepare(
            'INSERT INTO campaign_announcements (campaign_id, author_user_id, title, body)
             VALUES (:campaign_id, :author_user_id, :title, :body)'
        );
        $stmt->execute([
            ':campaign_id' => (int)$campaign_id,
            ':author_user_id' => (int)$gm_id,
            ':title' => $title,
            ':body' => $body,
        ]);

        $announcementId = (int)$this->pdo->lastInsertId();
        $this->createNotificationsForMembers(
            $campaign_id,
            (int)$gm_id,
            'campaign_announcement_created',
            $title,
            $body,
            ['announcement_id' => $announcementId]
        );
        return $announcementId;
    }

    public function updateAnnouncement($announcement_id, $gm_id, $title, $body) {
        $this->ensureCommunicationTablesExist();
        $stmt = $this->pdo->prepare(
            'UPDATE campaign_announcements ca
             JOIN campaigns c ON c.campaign_id = ca.campaign_id
             SET ca.title = :title, ca.body = :body
             WHERE ca.announcement_id = :announcement_id AND c.gm_id = :gm_id'
        );
        return $stmt->execute([
            ':title' => $title,
            ':body' => $body,
            ':announcement_id' => (int)$announcement_id,
            ':gm_id' => (int)$gm_id,
        ]);
    }

    public function deleteAnnouncement($announcement_id, $gm_id) {
        $this->ensureCommunicationTablesExist();
        $stmt = $this->pdo->prepare(
            'DELETE ca FROM campaign_announcements ca
             JOIN campaigns c ON c.campaign_id = ca.campaign_id
             WHERE ca.announcement_id = :announcement_id AND c.gm_id = :gm_id'
        );
        return $stmt->execute([
            ':announcement_id' => (int)$announcement_id,
            ':gm_id' => (int)$gm_id,
        ]);
    }

    private function createNotificationsForMembers($campaign_id, $excludedUserId, $type, $title, $body, $references = []) {
        $this->ensureCommunicationTablesExist();
        $this->ensureCampaignMemberTableExists();

        $stmt = $this->pdo->prepare(
            'SELECT DISTINCT user_id FROM campaign_members
             WHERE campaign_id = :campaign_id AND user_id <> :excluded_user_id'
        );
        $stmt->execute([
            ':campaign_id' => (int)$campaign_id,
            ':excluded_user_id' => (int)$excludedUserId,
        ]);

        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $userId) {
            $this->createNotification($userId, $campaign_id, $type, $title, $body, $references, $excludedUserId);
        }
    }

    public function createNotification($user_id, $campaign_id, $type, $title, $body, $references = [], $actor_user_id = null) {
        $this->ensureCommunicationTablesExist();
        $stmt = $this->pdo->prepare(
            'INSERT INTO notifications
             (user_id, campaign_id, type, title, body, announcement_id, session_id, actor_user_id)
             VALUES (:user_id, :campaign_id, :type, :title, :body, :announcement_id, :session_id, :actor_user_id)'
        );
        return $stmt->execute([
            ':user_id' => (int)$user_id,
            ':campaign_id' => $campaign_id !== null ? (int)$campaign_id : null,
            ':type' => $type,
            ':title' => $title,
            ':body' => $body,
            ':announcement_id' => isset($references['announcement_id']) ? (int)$references['announcement_id'] : null,
            ':session_id' => isset($references['session_id']) ? (int)$references['session_id'] : null,
            ':actor_user_id' => $actor_user_id !== null ? (int)$actor_user_id : null,
        ]);
    }

    public function getNotifications($user_id, $limit = 50) {
        $this->ensureCommunicationTablesExist();
        $limit = max(1, min(100, (int)$limit));
        $stmt = $this->pdo->prepare(
            "SELECT n.*, c.campaign_name
             FROM notifications n
             LEFT JOIN campaigns c ON c.campaign_id = n.campaign_id
             WHERE n.user_id = :user_id
             ORDER BY n.created_at DESC
             LIMIT $limit"
        );
        $stmt->execute([':user_id' => (int)$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnreadNotificationCount($user_id) {
        $this->ensureCommunicationTablesExist();
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND read_at IS NULL'
        );
        $stmt->execute([':user_id' => (int)$user_id]);
        return (int)$stmt->fetchColumn();
    }

    public function markNotificationRead($notification_id, $user_id) {
        $this->ensureCommunicationTablesExist();
        $stmt = $this->pdo->prepare(
            'UPDATE notifications SET read_at = CURRENT_TIMESTAMP
             WHERE notification_id = :notification_id AND user_id = :user_id'
        );
        return $stmt->execute([
            ':notification_id' => (int)$notification_id,
            ':user_id' => (int)$user_id,
        ]);
    }

    public function markAllNotificationsRead($user_id) {
        $this->ensureCommunicationTablesExist();
        $stmt = $this->pdo->prepare(
            'UPDATE notifications SET read_at = CURRENT_TIMESTAMP
             WHERE user_id = :user_id AND read_at IS NULL'
        );
        return $stmt->execute([':user_id' => (int)$user_id]);
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
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM campaign_members cm
                    WHERE cm.user_id = u.user_id
                      AND cm.campaign_id = :campaign_id
                )
                ORDER BY u.username ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':campaign_id' => $campaign_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableCharacters($campaign_id) {
        $this->ensureCampaignMemberTableExists();

        $sql = "SELECT c.character_id, c.character_name, c.player_id, u.username
                FROM characters c
                JOIN users u ON u.user_id = c.player_id
                WHERE c.campaign_id IS NULL
                ORDER BY u.username ASC, c.character_name ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableCharactersForPlayer($campaign_id, $player_id) {
        $sql = "SELECT character_id, character_name
                FROM characters
                WHERE player_id = :player_id AND campaign_id IS NULL
                ORDER BY character_name ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':player_id' => $player_id,
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isMember($campaign_id, $user_id) {
        return $this->isCampaignMember($campaign_id, $user_id);
    }

    public function joinPublicCampaign($campaign_id, $player_id, $character_id) {
        $this->ensureCampaignMemberTableExists();

        $campaign = $this->getById($campaign_id);
        if (!$campaign || (int)$campaign['gm_id'] === (int)$player_id) {
            return false;
        }

        $this->pdo->beginTransaction();

        try {
            $characterStmt = $this->pdo->prepare(
                "SELECT character_id FROM characters
                 WHERE character_id = :character_id
                   AND player_id = :player_id
                   AND campaign_id IS NULL
                 LIMIT 1"
            );
            $characterStmt->execute([
                ':character_id' => $character_id,
                ':player_id' => $player_id,
            ]);

            if (!$characterStmt->fetch()) {
                $this->pdo->rollBack();
                return false;
            }

            $characterUpdate = $this->pdo->prepare(
                "UPDATE characters SET campaign_id = :campaign_id
                 WHERE character_id = :character_id
                   AND player_id = :player_id
                   AND campaign_id IS NULL"
            );
            $characterUpdate->execute([
                ':campaign_id' => $campaign_id,
                ':character_id' => $character_id,
                ':player_id' => $player_id,
            ]);

            $memberSql = "INSERT INTO campaign_members (campaign_id, user_id, role)
                          VALUES (:campaign_id, :user_id, 'Player')
                          ON DUPLICATE KEY UPDATE role = VALUES(role)";
            $memberStmt = $this->pdo->prepare($memberSql);
            $memberStmt->execute([
                ':campaign_id' => $campaign_id,
                ':user_id' => $player_id,
            ]);

            $this->pdo->commit();
            $this->createNotification(
                $campaign['gm_id'],
                $campaign_id,
                'campaign_member_joined',
                'New player joined the campaign',
                'A player joined your campaign with a character.',
                [],
                $player_id
            );
            return true;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function addMember($campaign_id, $gm_id, $user_id, $role, $character_id = 0) {
        $this->ensureCampaignMemberTableExists();

        $campaign = $this->getById($campaign_id);
        if (!$campaign || (int)$campaign['gm_id'] !== (int)$gm_id) {
            return false;
        }

        $user_id = (int)$user_id;
        $character_id = (int)$character_id;
        $role = in_array($role, ['Player', 'Game Master'], true) ? $role : 'Player';

        if ($character_id > 0) {
            $characterStmt = $this->pdo->prepare(
                "SELECT player_id FROM characters
                 WHERE character_id = :character_id AND campaign_id IS NULL
                 LIMIT 1"
            );
            $characterStmt->execute([':character_id' => $character_id]);
            $character = $characterStmt->fetch(PDO::FETCH_ASSOC);
            if (!$character) {
                return false;
            }
            $user_id = (int)$character['player_id'];
        }

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

        $this->pdo->beginTransaction();

        try {
            if ($existingRow) {
                $updateSql = "UPDATE campaign_members SET role = :role WHERE campaign_id = :campaign_id AND user_id = :user_id";
                $updateStmt = $this->pdo->prepare($updateSql);
                $updateStmt->execute([
                    ':role' => $role,
                    ':campaign_id' => $campaign_id,
                    ':user_id' => $user_id,
                ]);
            } else {
                $insertSql = "INSERT INTO campaign_members (campaign_id, user_id, role) VALUES (:campaign_id, :user_id, :role)";
                $insertStmt = $this->pdo->prepare($insertSql);
                $insertStmt->execute([
                    ':campaign_id' => $campaign_id,
                    ':user_id' => $user_id,
                    ':role' => $role,
                ]);
            }

            if ($character_id > 0) {
                $characterUpdate = $this->pdo->prepare(
                    "UPDATE characters SET campaign_id = :campaign_id
                     WHERE character_id = :character_id AND player_id = :user_id AND campaign_id IS NULL"
                );
                $characterUpdate->execute([
                    ':campaign_id' => $campaign_id,
                    ':character_id' => $character_id,
                    ':user_id' => $user_id,
                ]);

                if ($characterUpdate->rowCount() !== 1) {
                    throw new RuntimeException('Character could not be assigned to campaign.');
                }
            }

            $this->pdo->commit();
            $this->createNotification(
                $user_id,
                $campaign_id,
                'campaign_member_added',
                'You were added to a campaign',
                'The Game Master added you to a campaign.',
                [],
                $gm_id
            );
            return true;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            return false;
        }
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
        $result = $stmt->execute([
            ':campaign_id' => $campaign_id,
            ':user_id' => $user_id,
        ]);

        if ($result && $stmt->rowCount() === 1) {
            $this->createNotification(
                $user_id,
                $campaign_id,
                'campaign_member_removed',
                'You were removed from a campaign',
                'The Game Master removed you from the campaign.',
                [],
                $gm_id
            );
        }

        return $result;
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

        $attendeeUserIds = array_map('intval', (array)$attendeeUserIds);
        $attendeeUserIds = array_unique(array_filter($attendeeUserIds, fn($id) => $id > 0));
        if (!empty($attendeeUserIds)) {
            $this->ensureCampaignMemberTableExists();
            $placeholders = implode(',', array_fill(0, count($attendeeUserIds), '?'));
            $memberStmt = $this->pdo->prepare(
                "SELECT user_id FROM campaign_members
                 WHERE campaign_id = ? AND user_id IN ($placeholders)"
            );
            $memberStmt->execute(array_merge([(int)$campaign_id], $attendeeUserIds));
            $validAttendeeIds = array_map('intval', $memberStmt->fetchAll(PDO::FETCH_COLUMN));
            if (count($validAttendeeIds) !== count($attendeeUserIds)) {
                return false;
            }
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

        $this->createNotificationsForMembers(
            $campaign_id,
            $gm_id,
            'campaign_session_note_created',
            $title,
            $summary ?: 'A new session note was added to the campaign.',
            ['session_id' => (int)$sessionId]
        );

        return (int)$sessionId;
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

    public function searchPublicCampaigns(string $query): array {
        // Sama kampanjajoukko kuin etusivulla; kutsukoodeja tai jäsentietoja ei haeta.
        $pattern = '%' . strtr($query, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
        $stmt = $this->pdo->prepare(
            "SELECT campaign_id, campaign_name, description FROM campaigns
             WHERE campaign_name LIKE :name ESCAPE '!' OR description LIKE :description ESCAPE '!'
             ORDER BY campaign_name, campaign_id LIMIT 21"
        );
        $stmt->execute([':name' => $pattern, ':description' => $pattern]);
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
