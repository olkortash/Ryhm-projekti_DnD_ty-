<?php
/*
 * Yhteinen sivun alku: HTML-otsakkeet, navigaatio ja istunnon kertaluonteinen palaute.
 * Näkymä voi asettaa $pageTitle-arvon ennen tämän tiedoston sisällyttämistä.
 * e() muuntaa tulostettavan arvon HTML-turvalliseksi tekstiksi ja attribuuttiarvoksi.
 */
if (!function_exists('e')) {
    function e($value): string {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

$pageTitle = $pageTitle ?? 'Masters';
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Masters campaign dashboard">

    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Finger+Paint&display=swap" rel="stylesheet">

</head>

<body>

<header class="topbar">
    <a class="brand" href="index.php?action=landing" aria-label="Masters home">MASTERS</a>

    <nav class="main-nav" aria-label="Main navigation">
        <a class="nav-link" href="index.php?action=landing#campaigns">Campaigns</a>
        <a class="nav-link" href="index.php?action=landing#features">Tools</a>
        <a class="nav-link" href="index.php?action=landing#features">Resources</a>
    </nav>

    <div class="account-actions">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="index.php?action=dashboard" class="text-link">Dashboard</a>
            <a href="index.php?action=notifications" class="text-link">Notifications<?php if (isset($notificationCount) && $notificationCount > 0): ?> (<?= (int)$notificationCount; ?>)<?php endif; ?></a>
            <a href="index.php?action=profile" class="text-link">Profile</a>
            <a href="index.php?action=logout" class="text-link">Log out</a>
        <?php else: ?>
            <a href="index.php?action=register" class="text-link">Register</a>
            <a href="index.php?action=login" class="avatar" aria-label="Sign in">Sign in</a>
        <?php endif; ?>
    </div>
</header>

<?php // Flash-viesti poistetaan istunnosta lukemisen yhteydessä, joten se näytetään vain kerran. ?>
<?php if (!empty($_SESSION['flash'])): ?>
    <?php
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    ?>
    <div class="flash-message flash-<?= e($flash['type'] ?? 'info'); ?>" role="status">
        <?= e($flash['message'] ?? ''); ?>
    </div>
<?php endif; ?>
