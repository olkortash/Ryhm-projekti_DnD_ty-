CREATE TABLE campaign_announcements (
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
);

CREATE TABLE notifications (
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
    FOREIGN KEY (actor_user_id) REFERENCES users(user_id) ON DELETE SET NULL
);
