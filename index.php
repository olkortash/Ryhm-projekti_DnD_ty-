<?php

declare(strict_types=1);

/*
 * MASTERS - Campaign Dashboard
 *
 * Yhteiset yhteydet:
 * - database
 * - session
 * - authentication
 * - helper functions
 */

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Campaign Dashboard';
$activeNav = 'campaigns';

// Ota käyttöön, kun dashboard vaatii kirjautumisen:
// require_login();


// -----------------------------------------------------------------------------
// Load campaigns
// -----------------------------------------------------------------------------

$campaigns = [];

if (is_logged_in()) {
    $stmt = $pdo->prepare("
        SELECT
            c.id,
            c.title,
            c.status,
            c.gm_note,
            c.next_session_at,
            c.cover_image,

            (
                SELECT COUNT(*)
                FROM campaign_players cp
                WHERE cp.campaign_id = c.id
                AND cp.status = 'accepted'
            ) AS players,

            (
                SELECT COUNT(*)
                FROM campaign_sessions cs
                WHERE cs.campaign_id = c.id
            ) AS sessions,

            (
                SELECT COUNT(*)
                FROM campaign_players cp
                WHERE cp.campaign_id = c.id
                AND cp.status = 'pending'
            ) AS pending

        FROM campaigns c
        WHERE c.owner_id = :owner_id
        ORDER BY c.updated_at DESC
    ");

    $stmt->execute([
        'owner_id' => current_user_id()
    ]);

    $campaigns = $stmt->fetchAll();
}


// -----------------------------------------------------------------------------
// Temporary development data
// Poista tämä, kun tietokanta on käytössä.
// -----------------------------------------------------------------------------

if (!$campaigns) {
    $campaigns = [
        [
            'id' => 1,
            'status' => 'ACTIVE',
            'title' => 'Echoes of the Sunken City',
            'players' => 4,
            'sessions' => 12,
            'next_session' => 'MAY 24, 2026',
            'pending' => 2,
            'gm_note' => 'Players have reached the Drowned Market. Introduce Selara the smuggler next session.',
            'tags' => ['Nautical', 'Mystery', 'Dungeon Crawl'],
            'art_class' => 'art-stars'
        ],
        [
            'id' => 2,
            'status' => 'PLANNING',
            'title' => 'The Verdant Heresy',
            'players' => 5,
            'sessions' => 0,
            'next_session' => 'JUN 7, 2026',
            'pending' => 5,
            'gm_note' => "Session zero scheduled. Need to finalize the druid circle's motivations and the inciting incident.",
            'tags' => ['Political Intrigue', 'Nature', 'High Fantasy'],
            'art_class' => 'art-forest'
        ]
    ];
}


require __DIR__ . '/partials/header.php';

?>

<main>

    <!-- Hero -->
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
                <a class="btn btn-primary" href="/campaigns/create.php">
                    Start a campaign <span aria-hidden="true">→</span>
                </a>

                <a class="btn btn-secondary" href="/resources/handbook.php">
                    GM handbook
                </a>
            </div>
        </div>
    </section>


    <!-- Campaigns -->
    <section class="dashboard" id="campaigns">

        <div class="section-head">
            <div>
                <p class="eyebrow">YOUR WORKSPACE</p>
                <h2>Your campaigns</h2>
            </div>

            <a class="btn btn-primary compact" href="/campaigns/create.php">
                <span aria-hidden="true">+</span>
                New campaign
            </a>
        </div>


        <div class="campaign-list">

            <?php foreach ($campaigns as $campaign): ?>

                <?php
                $status = strtolower($campaign['status'] ?? 'planning');
                $statusClass = $status === 'active' ? 'active' : 'planning';
                $artClass = $campaign['art_class']
                    ?? ($status === 'active' ? 'art-stars' : 'art-forest');
                ?>

                <article class="campaign-card">

                    <div class="campaign-art <?= e($artClass) ?>">

                        <span class="status <?= e($statusClass) ?>">
                            <?= e(strtoupper($status)) ?>
                        </span>

                        <div class="art-symbol" aria-hidden="true">

                            <?php if ($artClass === 'art-stars'): ?>

                                <span class="moon"></span>
                                <span class="silhouette"></span>

                            <?php else: ?>

                                <span class="leaf leaf-a"></span>
                                <span class="leaf leaf-b"></span>
                                <span class="leaf leaf-c"></span>

                            <?php endif; ?>

                        </div>
                    </div>


                    <div class="campaign-body">

                        <div class="campaign-top">

                            <div>
                                <p class="campaign-kicker">CAMPAIGN</p>
                                <h3><?= e($campaign['title']) ?></h3>
                            </div>

                            <span class="pending">
                                <span aria-hidden="true">◷</span>
                                <?= (int) ($campaign['pending'] ?? 0) ?> pending
                            </span>

                        </div>


                        <div class="stats">

                            <div class="stat">
                                <span class="stat-label">
                                    <span>♙</span> Players
                                </span>
                                <strong><?= (int) ($campaign['players'] ?? 0) ?></strong>
                            </div>

                            <div class="stat">
                                <span class="stat-label">
                                    <span>◷</span> Sessions
                                </span>
                                <strong><?= (int) ($campaign['sessions'] ?? 0) ?></strong>
                            </div>

                            <div class="stat">
                                <span class="stat-label">
                                    <span>▣</span> Next Session
                                </span>
                                <strong><?= e($campaign['next_session'] ?? '—') ?></strong>
                            </div>

                        </div>


                        <div class="gm-note">
                            <span class="note-label">GM NOTE</span>

                            <p>
                                <?= e($campaign['gm_note'] ?? 'No GM note yet.') ?>
                            </p>
                        </div>


                        <div class="campaign-footer">

                            <div class="tags">

                                <?php foreach ($campaign['tags'] ?? [] as $tag): ?>

                                    <span class="tag">
                                        <?= e($tag) ?>
                                    </span>

                                <?php endforeach; ?>

                            </div>


                            <!-- TODO: Vaihda tarvittaessa oman projektin URL:ksi. -->
                            <a
                                class="manage-link"
                                href="/campaigns/manage.php?id=<?= (int) $campaign['id'] ?>"
                            >
                                Manage <span aria-hidden="true">→</span>
                            </a>

                        </div>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

    </section>


    <!-- Main feature links -->
    <section class="feature-grid">

        <div class="feature-card">
            <span class="feature-icon">✦</span>

            <h3>Session tools</h3>

            <p>
                Prepare encounters, track initiative,
                and keep important notes close at hand.
            </p>

            <a href="/tools/">
                Explore tools →
            </a>
        </div>


        <div class="feature-card" id="resources">
            <span class="feature-icon">◈</span>

            <h3>GM resources</h3>

            <p>
                Keep your campaign references,
                worldbuilding material, and handbooks organized.
            </p>

            <a href="/resources/">
                Browse resources →
            </a>
        </div>


        <div class="feature-card">
            <span class="feature-icon">⌁</span>

            <h3>Player hub</h3>

            <p>
                Invite players and manage campaign access
                in one place.
            </p>

            <a href="/players/">
                Manage players →
            </a>
        </div>

    </section>

</main>

<?php require __DIR__ . '/partials/footer.php'; ?>