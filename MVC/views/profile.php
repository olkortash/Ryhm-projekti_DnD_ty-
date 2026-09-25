<?php
/*
 * Profiili: ProfileController välittää $user-tiedot ja kolme yhteenvetolukua.
 * $joinedCampaignCount laskee hahmojen eri kampanjat, ei hahmojen lukumäärää.
 */
$pageTitle = 'Profile - Roleplay App';
require __DIR__ . '/partials/head.php';

// Muotoillaan tilin luontipäivä; puuttuvalle arvolle käytetään oletustekstiä.
$memberSince = !empty($user['created_at'])
    ? date('F d, Y', strtotime($user['created_at']))
    : 'Unknown';
?>

<div class="dashboard">
    <div class="dashboard-hero">
        <h1><?= e($user['username']) ?></h1>
        <p>Account Information</p>
    </div>

    <?php // Käyttäjätilin perustiedot. ?>
    <section class="dashboard-section">
        <div class="section-head">
            <div>
                <p class="eyebrow">YOUR ACCOUNT</p>
                <h2>Account Details</h2>
            </div>
        </div>

        <div class="profile-card">
            <div class="profile-info-grid">
                <div class="profile-info-item">
                    <p class="profile-label">Username</p>
                    <p class="profile-value"><?= e($user['username']) ?></p>
                </div>

                <div class="profile-info-item">
                    <p class="profile-label">Email</p>
                    <p class="profile-value"><?= e($user['email']) ?></p>
                </div>

                <div class="profile-info-item">
                    <p class="profile-label">Member Since</p>
                    <p class="profile-value"><?= $memberSince ?></p>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-section">
        <div class="section-head">
            <div>
                <p class="eyebrow">YOUR ADVENTURE</p>
                <h2>Activity Summary</h2>
            </div>
        </div>

        <div class="profile-summary-grid">
            <div class="profile-summary-item">
                <span class="profile-summary-label">Characters</span>
                <strong><?= $characterCount ?></strong>
            </div>
            <div class="profile-summary-item">
                <span class="profile-summary-label">Created Campaigns</span>
                <strong><?= $createdCampaignCount ?></strong>
            </div>
            <div class="profile-summary-item">
                <span class="profile-summary-label">Joined Campaigns</span>
                <strong><?= $joinedCampaignCount ?></strong>
            </div>
        </div>
    </section>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
