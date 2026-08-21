<?php
/*
|--------------------------------------------------------------------------
| MASTERS - Campaign Dashboard
|--------------------------------------------------------------------------
|
| FUTURE BACKEND HOOKS:
| 1. Authentication / login:
|    - session_start();
|    - Check $_SESSION['user_id'] before showing the dashboard.
|    - Redirect unauthenticated users to /login.php.
|
| 2. Database connection:
|    - Require a shared PDO connection, e.g. require_once __DIR__ . '/config/db.php';
|    - Load campaigns with a prepared SELECT statement.
|
| 3. Campaign CRUD:
|    - "New Campaign" -> /campaign/create.php
|    - "Manage" -> /campaign/edit.php?id=...
|    - Add authorization checks so users can only manage their own campaigns.
|
| 4. Security:
|    - Use prepared PDO statements.
|    - Escape dynamic output with htmlspecialchars().
|    - Add CSRF tokens to POST forms.
|    - Validate all IDs and form fields server-side.
|
| 5. Other future features:
|    - Player invitations / pending requests
|    - Session scheduling
|    - GM notes
|    - Campaign tags
|    - File/image uploads
|    - Notifications / flash messages
*/

// FUTURE: session_start();
// FUTURE: require_once __DIR__ . '/config/db.php';
// FUTURE: require_once __DIR__ . '/includes/auth.php';

$campaigns = [
    [
        'status' => 'ACTIVE',
        'status_class' => 'active',
        'title' => 'Echoes of the Sunken City',
        'players' => 4,
        'sessions' => 12,
        'next_session' => 'MAY 24, 2026',
        'pending' => 2,
        'note' => 'Players have reached the Drowned Market. Introduce Selara the smuggler next session.',
        'tags' => ['Nautical', 'Mystery', 'Dungeon Crawl'],
        'art_class' => 'art-stars'
    ],
    [
        'status' => 'PLANNING',
        'status_class' => 'planning',
        'title' => 'The Verdant Heresy',
        'players' => 5,
        'sessions' => 0,
        'next_session' => 'JUN 7, 2026',
        'pending' => 5,
        'note' => "Session zero scheduled. Need to finalize the druid circle's motivations and the inciting incident.",
        'tags' => ['Political Intrigue', 'Nature', 'High Fantasy'],
        'art_class' => 'art-forest'
    ]
];

// FUTURE: replace the sample array above with:
// $campaigns = $pdo->query('SELECT ... FROM campaigns WHERE owner_id = ?')->fetchAll();

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Masters campaign dashboard">
    <title>Masters — Campaign Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- FUTURE: Show a logged-in user's name/avatar here after authentication. -->
    <header class="topbar">
        <a class="brand" href="#" aria-label="Masters home">MASTERS</a>

        <nav class="main-nav" aria-label="Main navigation">
            <a class="nav-link active" href="#campaigns">Campaigns</a>
            <a class="nav-link" href="#tools">Tools</a>
            <a class="nav-link" href="#resources">Resources</a>
        </nav>

        <div class="account-actions">
            <!-- FUTURE: Replace with /login.php when authentication is implemented. -->
            <a href="#" class="text-link">Sign in</a>
            <a href="#" class="avatar" aria-label="Account">GM</a>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="hero-glow glow-one"></div>
            <div class="hero-glow glow-two"></div>
            <div class="hero-content">
                <p class="eyebrow">THE GAMEMASTER'S SANCTUM</p>
                <h1>Craft worlds.<br><span>Guide legends.</span></h1>
                <p class="hero-copy">
                    Every legend needs a keeper. Craft worlds, guide heroes, and weave
                    stories that your players will remember for years.
                </p>

                <div class="hero-actions">
                    <!-- FUTURE: Point this button to /campaign/create.php. -->
                    <a class="btn btn-primary" href="#new-campaign">
                        Start a campaign <span aria-hidden="true">→</span>
                    </a>
                    <!-- FUTURE: Link to a real GM handbook / CMS-managed resource. -->
                    <a class="btn btn-secondary" href="#handbook">GM handbook</a>
                </div>
            </div>
        </section>

        <section class="dashboard" id="campaigns">
            <div class="section-head">
                <div>
                    <p class="eyebrow">YOUR WORKSPACE</p>
                    <h2>Your campaigns</h2>
                </div>

                <!-- FUTURE: This can become a POST form with CSRF protection. -->
                <a class="btn btn-primary compact" id="new-campaign" href="#">
                    <span aria-hidden="true">+</span> New campaign
                </a>
            </div>

            <div class="campaign-list">
                <?php foreach ($campaigns as $campaign): ?>
                    <article class="campaign-card">
                        <div class="campaign-art <?= e($campaign['art_class']) ?>">
                            <span class="status <?= e($campaign['status_class']) ?>">
                                <?= e($campaign['status']) ?>
                            </span>
                            <div class="art-symbol" aria-hidden="true">
                                <?php if ($campaign['art_class'] === 'art-stars'): ?>
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
                                    <?= (int) $campaign['pending'] ?> pending
                                </span>
                            </div>

                            <div class="stats">
                                <div class="stat">
                                    <span class="stat-label"><span>♙</span> Players</span>
                                    <strong><?= (int) $campaign['players'] ?></strong>
                                </div>
                                <div class="stat">
                                    <span class="stat-label"><span>◷</span> Sessions</span>
                                    <strong><?= (int) $campaign['sessions'] ?></strong>
                                </div>
                                <div class="stat">
                                    <span class="stat-label"><span>▣</span> Next Session</span>
                                    <strong><?= e($campaign['next_session']) ?></strong>
                                </div>
                            </div>

                            <div class="gm-note">
                                <span class="note-label">GM NOTE</span>
                                <p><?= e($campaign['note']) ?></p>
                            </div>

                            <div class="campaign-footer">
                                <div class="tags">
                                    <?php foreach ($campaign['tags'] as $tag): ?>
                                        <span class="tag"><?= e($tag) ?></span>
                                    <?php endforeach; ?>
                                </div>

                                <!-- FUTURE: Link to /campaign/manage.php?id=... -->
                                <a class="manage-link" href="#">
                                    Manage <span aria-hidden="true">→</span>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="feature-grid" id="tools">
            <div class="feature-card">
                <span class="feature-icon">✦</span>
                <h3>Session tools</h3>
                <p>Prepare encounters, track initiative, and keep important notes close at hand.</p>
                <!-- FUTURE: Link to authenticated GM tools. -->
                <a href="#">Explore tools →</a>
            </div>

            <div class="feature-card" id="resources">
                <span class="feature-icon">◈</span>
                <h3>GM resources</h3>
                <p>Keep your campaign references, worldbuilding material, and handbooks organized.</p>
                <!-- FUTURE: Load resources from database/CMS. -->
                <a href="#" id="handbook">Browse resources →</a>
            </div>

            <div class="feature-card">
                <span class="feature-icon">⌁</span>
                <h3>Player hub</h3>
                <p>Invite players and keep pending requests, characters, and campaign access in one place.</p>
                <!-- FUTURE: Connect to player invitation / account system. -->
                <a href="#">Manage players →</a>
            </div>
        </section>
    </main>

    <footer class="footer">
        <span>MASTERS</span>
        <p>Campaign dashboard · Built for storytellers</p>
        <!-- FUTURE: Add legal links, account settings, logout and support links. -->
    </footer>
</body>
</html>
