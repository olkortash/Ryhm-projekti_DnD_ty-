<?php
$pageTitle = 'Profile - Roolipelisovellus';
require __DIR__ . '/partials/head.php';

// Format the creation date
$memberSince = !empty($user['created_at']) 
    ? date('F d, Y', strtotime($user['created_at'])) 
    : 'Unknown';
?>

<div class="dashboard">

    <div class="dashboard-hero">
        <h1><?= e($user['username']) ?></h1>
        <p>Account Information</p>
    </div>

    <!-- Account Details Section -->
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

</div>

<?php require __DIR__ . '/partials/footer.php'; ?>