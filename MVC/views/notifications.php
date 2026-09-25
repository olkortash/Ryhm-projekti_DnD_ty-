<?php
/*
 * Ilmoitussivu: CampaignController välittää käyttäjän $notifications-listan.
 * read_at === null merkitsee lukematonta ilmoitusta; kuittaukset lähetetään POST-lomakkeilla.
 */
$pageTitle = 'Notifications - Roolipelisovellus';
require __DIR__ . '/partials/head.php';
?>

<main class="dashboard notifications-page">
    <div class="section-head">
        <div>
            <p class="eyebrow">CAMPAIGN ACTIVITY</p>
            <h1>Notifications</h1>
        </div>
        <?php if (!empty($notifications)): ?>
            <form action="index.php?action=notifications_read_all" method="POST">
                <button type="submit" class="btn btn-secondary">Mark all as read</button>
            </form>
        <?php endif; ?>
    </div>

    <?php if (empty($notifications)): ?>
        <div class="empty-state">
            <p>You have no notifications yet.</p>
        </div>
    <?php else: ?>
        <div class="notification-list">
            <?php foreach ($notifications as $notification): ?>
                <article class="info-card notification-card <?= $notification['read_at'] === null ? 'notification-unread' : ''; ?>">
                    <div>
                        <p class="eyebrow"><?= e($notification['campaign_name'] ?? 'Campaign'); ?></p>
                        <h2><?= e($notification['title']); ?></h2>
                        <p><?= nl2br(e($notification['body'])); ?></p>
                        <small><?= e($notification['created_at']); ?></small>
                    </div>
                    <?php if ($notification['read_at'] === null): ?>
                        <form action="index.php?action=notification_read" method="POST">
                            <input type="hidden" name="notification_id" value="<?= (int)$notification['notification_id']; ?>">
                            <button type="submit" class="btn btn-primary compact">Mark as read</button>
                        </form>
                    <?php else: ?>
                        <span class="notification-status">Read</span>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
