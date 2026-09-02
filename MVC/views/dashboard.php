<?php 
$pageTitle = "Dashboard - Roolipelisovellus";
require __DIR__ . '/partials/head.php'; 
?>

<div class="dashboard">

    <div class="dashboard-hero">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p>Manage your characters and campaigns in one place</p>
    </div>

    <!-- Characters Section -->
    <section class="dashboard-section">
        <div class="section-head">
            <div>
                <p class="eyebrow">MY CHARACTERS</p>
                <h2>Characters</h2>
            </div>
            <a class="btn btn-primary compact" href="index.php?action=character_create">
                <span aria-hidden="true">+</span>New Character
            </a>
        </div>

        <?php if (!empty($characters)): ?>
            <div class="dashboard-list">
                <?php foreach ($characters as $char): ?>
                    <article class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h3>
                                <a class="text-link" href="index.php?action=character_view&id=<?= $char['character_id']; ?>">
                                    <?= htmlspecialchars($char['character_name']); ?>
                                </a>
                            </h3>
                            <p class="dashboard-card-meta">
                                Lvl <?= $char['level']; ?> • 
                                <?= $char['race_name']; ?> • 
                                <?= $char['class_name']; ?>
                            </p>
                        </div>
                        <div class="dashboard-card-footer">
                            <a class="manage-link" href="index.php?action=character_view&id=<?= $char['character_id']; ?>">
                                View →
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>You have no characters yet.</p>
                <a class="btn btn-primary" href="index.php?action=character_create">Create your first character</a>
            </div>
        <?php endif; ?>
    </section>

    <!-- Campaigns Section (Game Master) -->
    <section class="dashboard-section">
        <div class="section-head">
            <div>
                <p class="eyebrow">GAME MASTER</p>
                <h2>Campaigns</h2>
            </div>
            <button class="btn btn-primary compact" onclick="document.getElementById('campaign-form').style.display = document.getElementById('campaign-form').style.display === 'none' ? 'block' : 'none'">
                <span aria-hidden="true">+</span>New Campaign
            </button>
        </div>

        <form id="campaign-form" class="dashboard-form auth-form" action="index.php?action=campaign_create" method="POST" style="display: none; margin-bottom: 24px;">
            <label>Campaign Name</label>
            <input type="text" name="campaign_name" placeholder="E.g. Kingdoms at War" required>
            
            <label>Description</label>
            <input type="text" name="description" placeholder="Brief description of your campaign">
            
            <button type="submit" class="btn btn-primary auth-submit">Create Campaign</button>
        </form>

        <?php if (!empty($gmCampaigns)): ?>
            <div class="dashboard-list">
                <?php foreach ($gmCampaigns as $camp): ?>
                    <article class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h3>
                                <a class="text-link" href="index.php?action=campaign_view&id=<?= $camp['campaign_id']; ?>">
                                    <?= htmlspecialchars($camp['campaign_name']); ?>
                                </a>
                            </h3>
                            <p class="dashboard-card-meta">
                                Invite Code: <strong><?= $camp['invite_code']; ?></strong>
                            </p>
                            <?php if (!empty($camp['description'])): ?>
                                <p class="dashboard-card-desc">
                                    <?= htmlspecialchars($camp['description']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="dashboard-card-footer">
                            <a class="manage-link" href="index.php?action=campaign_view&id=<?= $camp['campaign_id']; ?>">
                                Manage →
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>You have no campaigns yet.</p>
                <button class="btn btn-primary" onclick="document.getElementById('campaign-form').style.display = 'block'">Create your first campaign</button>
            </div>
        <?php endif; ?>
    </section>

</div>

<?php require __DIR__ . '/partials/footer.php'; ?>