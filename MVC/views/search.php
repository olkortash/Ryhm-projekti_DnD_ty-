<?php
/*
 * SearchController välittää hakusanan, virheen ja kaksi rajattua tuloslistaa.
 * Käyttäjistä näytetään vain käyttäjänimi. Kampanjan avaaminen käyttää nykyistä käyttöoikeustarkistusta.
 */
$pageTitle = 'Search - Masters';
require __DIR__ . '/partials/head.php';
?>

<main class="dashboard search-page">
    <div class="dashboard-hero">
        <p class="eyebrow">FIND YOUR TABLE</p>
        <h1>Search</h1>
        <?php if ($searchError !== null): ?>
            <p role="alert"><?= e($searchError); ?></p>
        <?php elseif ($searchQuery === ''): ?>
            <p>Use the search field above to find users and campaigns.</p>
        <?php else: ?>
            <p>Results for “<?= e($searchQuery); ?>”</p>
        <?php endif; ?>
    </div>

    <?php if ($searchQuery !== '' && $searchError === null): ?>
        <div class="search-results">
            <section class="dashboard-section" aria-labelledby="search-users-title">
                <div class="section-head">
                    <h2 id="search-users-title">Users (<?= count($users); ?><?= $moreUsers ? '+' : ''; ?>)</h2>
                </div>
                <?php if (empty($users)): ?>
                    <p class="empty-state">No users found.</p>
                <?php else: ?>
                    <ul class="search-result-list">
                        <?php foreach ($users as $user): ?>
                            <li class="search-result-card"><?= e($user['username']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if ($moreUsers): ?>
                        <p class="form-help">Showing the first 20 users. Refine your search to find more specific matches.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </section>

            <section class="dashboard-section" aria-labelledby="search-campaigns-title">
                <div class="section-head">
                    <h2 id="search-campaigns-title">Campaigns (<?= count($campaigns); ?><?= $moreCampaigns ? '+' : ''; ?>)</h2>
                </div>
                <?php if (empty($campaigns)): ?>
                    <p class="empty-state">No campaigns found.</p>
                <?php else: ?>
                    <ul class="search-result-list">
                        <?php foreach ($campaigns as $campaign): ?>
                            <li class="search-result-card">
                                <h3><?= e($campaign['campaign_name']); ?></h3>
                                <?php if (!empty($campaign['description'])): ?>
                                    <p><?= e($campaign['description']); ?></p>
                                <?php endif; ?>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <a class="manage-link" href="index.php?action=campaign_view&amp;id=<?= (int) $campaign['campaign_id']; ?>">View campaign →</a>
                                <?php else: ?>
                                    <a class="manage-link" href="index.php?action=login">Sign in to view campaign →</a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if ($moreCampaigns): ?>
                        <p class="form-help">Showing the first 20 campaigns. Refine your search to find more specific matches.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </section>
        </div>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
