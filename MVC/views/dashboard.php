<?php
/*
 * Hallintapaneeli: DashboardController välittää omat hahmot ($characters)
 * ja käyttäjän luomat kampanjat ($gmCampaigns). Tyhjille listoille näytetään aloitusohje.
 */
$pageTitle = "Dashboard - Roleplay App";

$highlightNewCampaign =
    isset($_GET['new_campaign']) &&
    $_GET['new_campaign'] === '1';
require __DIR__ . '/partials/head.php';
?>



<div class="dashboard">
    <div class="dashboard-hero">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p>Manage your characters and campaigns in one place</p>
    </div>

    <?php // Omat hahmot ja niiden kampanjalinkit. ?>
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
            <div class="dashboard-list dashboard-list-compact">
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
                            <p class="dashboard-card-campaign">
                                <span>Campaign:</span>
                                <?php if (!empty($char['campaign_id']) && !empty($char['campaign_name'])): ?>
                                    <a href="index.php?action=campaign_view&id=<?= $char['campaign_id']; ?>">
                                        <?= htmlspecialchars($char['campaign_name']); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="dashboard-card-campaign-empty">No campaign</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="dashboard-card-footer">
                            <a class="dashboard-card-action dashboard-card-action-primary" href="index.php?action=character_view&id=<?= $char['character_id']; ?>">
                                View Character
                            </a>
                            <?php if (!empty($char['campaign_id'])): ?>
                                <form action="index.php?action=character_unlink_campaign" method="POST" onsubmit="return confirm('Remove this character from the campaign?');">
                                    <?= csrf_field(); ?>
                                    <input type="hidden" name="character_id" value="<?= $char['character_id']; ?>">
                                    <button type="submit" class="btn btn-danger compact">Leave campaign</button>
                                </form>
                            <?php endif; ?>
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

    <?php // Käyttäjän luomat kampanjat; painikkeet avaavat saman piilotetun luontilomakkeen. ?>
    <section class="dashboard-section" id="campaigns-section">
        <div class="section-head">
            <div>
                <p class="eyebrow">GAME MASTER</p>
                <h2>Campaigns</h2>
            </div>
            <button
                class="btn btn-primary compact"
                onclick="document.getElementById('campaign-form').style.display = document.getElementById('campaign-form').style.display === 'none' ? 'block' : 'none'"
            >
                <span aria-hidden="true">+</span>New Campaign
            </button>
        </div>

        <form id="campaign-form" class="dashboard-form auth-form <?= $highlightNewCampaign ? 'campaign-highlight' : ''; ?>" action="index.php?action=campaign_create" method="POST" style="display: <?= $highlightNewCampaign ? 'block' : 'none'; ?>; margin-bottom: 45px;">
            <?= csrf_field(); ?>
            <label class="field-label"><span>Campaign Name</span><span class="field-required">Required</span></label>
            <input type="text" name="campaign_name" placeholder="E.g. Kingdoms at War" required>

            <label class="field-label"><span>Description</span><span class="field-optional">Optional</span></label>
            <input type="text" name="description" placeholder="Brief description of your campaign">

            <button type="submit" class="btn btn-primary auth-submit">Create Campaign</button>
        </form>

        <?php if (!empty($gmCampaigns)): ?>
            <div class="dashboard-list dashboard-list-compact">
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
                            <a class="dashboard-card-action dashboard-card-action-primary" href="index.php?action=campaign_view&id=<?= $camp['campaign_id']; ?>">
                                Manage Campaign
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

<?php if ($highlightNewCampaign): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('campaign-form');

    if (form) {
        setTimeout(function () {
            form.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            const nameInput = form.querySelector(
                'input[name="campaign_name"]'
            );

            if (nameInput) {
                setTimeout(function () {
                    nameInput.focus();
                }, 700);
            }
        }, 200);
    }
});
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const campaignForm = document.getElementById('campaign-form');

    if (!campaignForm) {
        return;
    }

    campaignForm.addEventListener('submit', function () {
        const submitButtons = campaignForm.querySelectorAll('button[type="submit"], input[type="submit"]');
        submitButtons.forEach(function (button) {
            button.disabled = true;
            button.setAttribute('aria-disabled', 'true');
        });
    }, { once: true });
});
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
