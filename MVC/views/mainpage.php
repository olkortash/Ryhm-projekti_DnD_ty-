<?php
/*
 * Etusivu: AuthController välittää julkiset kampanjat $campaigns-taulukossa.
 * Istunnon tila määrää, ohjataanko käyttäjä kampanjaan vai kirjautumaan.
 */
$pageTitle = "Main page - Roleplay App";
require __DIR__ . '/partials/head.php';
?>

<main>

    <?php // Aloitusalue ja tärkeimmät toimintolinkit. ?>
    <section class="hero">
        <div class="hero-glow glow-one"></div>
        <div class="hero-glow glow-two"></div>
        <div class="hero-content">
            <p class="eyebrow">THE GAMEMASTER'S SANCTUM</p>
            <h1>
                Craft worlds.<br>
                <span>Guide legends.</span>
            </h1>
            <p class="hero-copy">
                Every legend needs a keeper.
                Craft worlds, guide heroes, and weave stories
                that your players will remember for years.
            </p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="index.php?action=dashboard">
                    Start a campaign
                    <span aria-hidden="true">→</span>
                </a>
                <a class="btn btn-secondary" href="index.php?action=landing#features">
                    GM resources
                </a>
            </div>
        </div>
    </section>

    <?php // Julkiset kampanjat tai tyhjän listan aloitusohje. ?>
    <section class="dashboard" id="campaigns">
        <div class="section-head">
            <div>
                <p class="eyebrow">COMMUNITY</p>
                <h2>Public campaigns</h2>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a class="btn btn-primary compact" href="index.php?action=dashboard">
                    <span aria-hidden="true">+</span>
                    New campaign
                </a>
            <?php endif; ?>
        </div>
        <div class="campaign-layout">
            <div class="campaign-list-panel">
                <?php if (empty($campaigns)): ?>
                    <div class="empty-state">
                        <h3>No public campaigns yet</h3>
                        <p>Be the first to create a campaign for the community.</p>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a class="btn btn-primary" href="index.php?action=dashboard">
                                Create campaign
                            </a>
                        <?php else: ?>
                            <a class="btn btn-primary" href="index.php?action=login">
                                Log in to create one
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="campaign-list">
                        <?php foreach ($campaigns as $campaign): ?>
                            <article class="campaign-card">
                                <div class="campaign-art art-stars">
                                    <span class="status active"> CAMPAIGN </span>
                                    <div class="art-symbol" aria-hidden="true">
                                        <span class="moon"></span>
                                        <span class="silhouette"></span>
                                    </div>
                                </div>
                                <div class="campaign-body">
                                    <div class="campaign-top">
                                        <div>
                                            <p class="campaign-kicker">CAMPAIGN</p>
                                            <h3>
                                                <?= e($campaign['campaign_name'] ?? 'Untitled campaign') ?>
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="stats">
                                        <div class="stat">
                                            <span class="stat-label">
                                                <span aria-hidden="true"> ♙ </span>
                                                Characters
                                            </span>
                                            <strong>
                                                <?= (int) ($campaign['character_count'] ?? 0) ?>
                                            </strong>
                                        </div>
                                        <div class="stat">
                                            <span class="stat-label">
                                                <span aria-hidden="true"> ▣ </span>
                                                Created
                                            </span>
                                            <strong>
                                                <?php
                                                // Puuttuva tai virheellinen luontipäivä näytetään Unknown-tekstinä.
                                                $createdAt = $campaign['created_at'] ?? null;
                                                if ($createdAt) {
                                                    $timestamp = strtotime($createdAt);
                                                    echo $timestamp !== false
                                                        ? e(date('M j, Y', $timestamp))
                                                        : 'Unknown';
                                                } else {
                                                    echo 'Unknown';
                                                }
                                                ?>
                                            </strong>
                                        </div>
                                    </div>
                                    <div class="gm-note">
                                        <span class="note-label"> DESCRIPTION </span>
                                        <p>
                                            <?= e(
                                                $campaign['description']
                                                ?? 'No description yet.'
                                            ) ?>
                                        </p>
                                    </div>
                                    <div class="campaign-footer">
                                        <span class="tag">
                                            <?= (int) (
                                                $campaign['character_count'] ?? 0
                                            ) ?>
                                            characters
                                        </span>
                                        <?php if (isset($campaign['campaign_id'])): ?>
                                            <?php if (isset($_SESSION['user_id'])): ?>
                                                <a class="manage-link" href="index.php?action=campaign_view&id=<?= (int) $campaign['campaign_id'] ?>">
                                                    Join campaign
                                                    <span aria-hidden="true"> → </span>
                                                </a>
                                            <?php else: ?>
                                                <a class="manage-link" href="index.php?action=login">
                                                    Log in to join
                                                    <span aria-hidden="true"> → </span>
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <aside class="campaign-sidebar" aria-label="Campaign advertisement">
                <div class="ad-banner">
                    <span class="ad-kicker">SPONSORED</span>
                    <div class="ad-banner-image-wrap">
                        <img src="../assets/images/ad-banner.png" alt="Advertisement banner" />
                    </div>
            
                </div>
            </aside>
        </div>
    </section>
    <?php
    $recentActivities = [];
    if (!empty($recentActivity)) {
        foreach ($recentActivity as $activity) {
            $activityTime = $activity['activity_time'] ?? null;
            $recentActivities[] = [
                'time' => $activityTime ? date('M j', strtotime((string) $activityTime)) : 'Today',
                'text' => $activity['activity_text'] ?? 'Campaign activity',
            ];
        }
    }

    if (empty($recentActivities)) {
        $recentActivities = [[
            'time' => 'Now',
            'text' => 'No recent activity yet',
        ]];
    }

    $upcomingSessionItems = [];
    if (!empty($upcomingSessions)) {
        foreach ($upcomingSessions as $session) {
            $sessionDate = $session['session_date'] ?? null;
            $upcomingSessionItems[] = [
                'date' => $sessionDate ? date('M j', strtotime((string) $sessionDate)) : 'No date set',
                'title' => $session['campaign_name'] ?? ($session['title'] ?? 'Campaign session'),
            ];
        }
    }

    if (empty($upcomingSessionItems)) {
        $upcomingSessionItems = [[
            'date' => 'No date set',
            'title' => 'No upcoming sessions yet',
        ]];
    }

    $openCampaigns = [];
    foreach (array_slice($campaigns, 0, 3) as $campaign) {
        $campaignId = (int) ($campaign['campaign_id'] ?? 0);
        $campaignName = $campaign['campaign_name'] ?? 'Untitled campaign';
        $description = trim((string) ($campaign['description'] ?? ''));
        $characterCount = (int) ($campaign['character_count'] ?? 0);
        $spotsOpen = max(1, 4 - $characterCount);

        $openCampaigns[] = [
            'id' => $campaignId,
            'name' => $campaignName,
            'meta' => $description !== ''
                ? mb_substr($description, 0, 28) . (mb_strlen($description) > 28 ? '…' : '')
                : 'Story campaign',
            'spots' => $spotsOpen . ' spots open',
        ];
    }

    if (empty($openCampaigns)) {
        $openCampaigns = [[
            'name' => 'No public campaigns yet',
            'meta' => 'Campaign listing is empty',
            'spots' => '0 spots open',
        ]];
    }
    ?>
    <section class="feature-grid">
        <article class="feature-card activity-card">
            <div class="feature-header">
                <span class="feature-icon" aria-hidden="true"> ✦ </span>
                <span class="feature-badge">Live</span>
            </div>
            <h3>Recent activity</h3>
            <ul class="feature-list">
                <?php foreach ($recentActivities as $activity): ?>
                    <li>
                        <span class="feature-timestamp"><?= e($activity['time']) ?></span>
                        <span><?= e($activity['text']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <a class="feature-link" href="index.php?action=dashboard">
                View all activity →
            </a>
        </article>

        <article class="feature-card session-card">
            <div class="feature-header">
                <span class="feature-icon" aria-hidden="true"> ◈ </span>
                <span class="feature-badge">Calendar</span>
            </div>
            <h3>Upcoming sessions</h3>
            <ul class="feature-list">
                <?php foreach ($upcomingSessionItems as $session): ?>
                    <li>
                        <span class="feature-timestamp"><?= e($session['date']) ?></span>
                        <span><?= e($session['title']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <a class="feature-link" href="index.php?action=dashboard">
                View calendar →
            </a>
        </article>

        <article class="feature-card campaign-card">
            <div class="feature-header">
                <span class="feature-icon" aria-hidden="true"> ⌁ </span>
                <span class="feature-badge">Open</span>
            </div>
            <h3>Looking for players</h3>
            <ul class="feature-list">
                <?php foreach ($openCampaigns as $campaign): ?>
                    <li>
                        <?php if (!empty($campaign['id'])): ?>
                            <a class="feature-campaign-link" href="index.php?action=campaign_view&id=<?= (int) $campaign['id'] ?>">
                                <strong><?= e($campaign['name']) ?></strong>
                            </a>
                        <?php else: ?>
                            <strong><?= e($campaign['name']) ?></strong>
                        <?php endif; ?>
                        <span class="feature-meta"><?= e($campaign['meta']) ?></span>
                        <span class="feature-spots"><?= e($campaign['spots']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <a class="feature-link" href="index.php?action=landing#campaigns">
                View campaign →
            </a>
        </article>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
