<?php 
$pageTitle = "Main page - Roolipelisovellus";
require __DIR__ . '/partials/head.php'; 
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

                <a
                    class="btn btn-primary"
                    href="/campaigns/create.php"
                >
                    Start a campaign
                    <span aria-hidden="true">→</span>
                </a>

                <a
                    class="btn btn-secondary"
                    href="/resources/"
                >
                    GM resources
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

            <a
                class="btn btn-primary compact"
                href="/campaigns/create.php"
            >
                <span aria-hidden="true">+</span>
                New campaign
            </a>

        </div>


        <?php if (!$campaigns): ?>

            <div class="empty-state">

                <h3>No campaigns yet</h3>

                <p>
                    Create your first campaign and start building your world.
                </p>

                <a
                    class="btn btn-primary"
                    href="/campaigns/create.php"
                >
                    Create campaign
                </a>

            </div>

        <?php else: ?>

            <div class="campaign-list">

                <?php foreach ($campaigns as $campaign): ?>

                    <article class="campaign-card">

                        <div class="campaign-art art-stars">

                            <span class="status active">
                                CAMPAIGN
                            </span>

                            <div
                                class="art-symbol"
                                aria-hidden="true"
                            >
                                <span class="moon"></span>
                                <span class="silhouette"></span>
                            </div>

                        </div>


                        <div class="campaign-body">

                            <div class="campaign-top">

                                <div>

                                    <p class="campaign-kicker">
                                        CAMPAIGN
                                    </p>

                                    <h3>
                                        <?= e($campaign['campaign_name']) ?>
                                    </h3>

                                </div>

                            </div>


                            <div class="stats">

                                <div class="stat">

                                    <span class="stat-label">
                                        <span>♙</span>
                                        Characters
                                    </span>

                                    <strong>
                                        <?= (int) $campaign['character_count'] ?>
                                    </strong>

                                </div>


                                <div class="stat">

                                    <span class="stat-label">
                                        <span>▣</span>
                                        Created
                                    </span>

                                    <strong>
                                        <?= e(
                                            date(
                                                'M j, Y',
                                                strtotime($campaign['created_at'])
                                            )
                                        ) ?>
                                    </strong>

                                </div>


                                <div class="stat">

                                    <span class="stat-label">
                                        <span>⌁</span>
                                        Invite code
                                    </span>

                                    <strong>
                                        <?= e($campaign['invite_code']) ?>
                                    </strong>

                                </div>

                            </div>


                            <div class="gm-note">

                                <span class="note-label">
                                    DESCRIPTION
                                </span>

                                <p>
                                    <?= e(
                                        $campaign['description']
                                        ?: 'No description yet.'
                                    ) ?>
                                </p>

                            </div>


                            <div class="campaign-footer">

                                <span class="tag">
                                    <?= (int) $campaign['character_count'] ?>
                                    characters
                                </span>


                                <a
                                    class="manage-link"
                                    href="/campaigns/manage.php?id=<?= (int) $campaign['campaign_id'] ?>"
                                >
                                    Manage
                                    <span aria-hidden="true">→</span>
                                </a>

                            </div>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </section>


    <!-- Features -->
    <section class="feature-grid">

        <div class="feature-card">

            <span class="feature-icon">✦</span>

            <h3>Characters</h3>

            <p>
                Create and manage the characters belonging
                to your campaigns.
            </p>

            <a href="/characters/">
                Manage characters →
            </a>

        </div>


        <div class="feature-card">

            <span class="feature-icon">◈</span>

            <h3>Campaigns</h3>

            <p>
                Create campaigns, manage their information,
                and share invite codes with players.
            </p>

            <a href="/campaigns/">
                Manage campaigns →
            </a>

        </div>


        <div class="feature-card">

            <span class="feature-icon">⌁</span>

            <h3>Resources</h3>

            <p>
                Keep your campaign references and
                worldbuilding material organized.
            </p>

            <a href="/resources/">
                Browse resources →
            </a>

        </div>

    </section>

</main>

<?php require __DIR__ . '/partials/footer.php'; ?>