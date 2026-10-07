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

// Keep directly rendered views and isolated tests functional; normal requests
// define these helpers in public/index.php before routing.
if (!function_exists('csrf_token')) {
    function csrf_token(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return (string)$_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string {
        return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
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

</head>

<body>

<header class="topbar">
    <a class="brand" href="index.php?action=landing" aria-label="Masters home">MASTERS</a>

    <nav class="main-nav" aria-label="Main navigation">
        <a class="nav-link" href="index.php?action=landing#campaigns">Campaigns</a>
        <!-- <a class="nav-link" href="index.php?action=landing#features">Tools</a>
        <a class="nav-link" href="index.php?action=landing#features">Resources</a> -->
    </nav>

    <?php // GET-haku säilyttää hakusanan URL:ssa ja toimii myös ilman JavaScriptiä. ?>
    <form class="header-search" action="index.php" method="GET" role="search" aria-label="Site search">
        <input type="hidden" name="action" value="search">
        <input
            type="search"
            name="q"
            value="<?= e($searchQuery ?? ''); ?>"
            placeholder="Users & campaigns"
            aria-label="Search users and campaigns"
            maxlength="100"
            required
        >
        <button type="submit">Search</button>
    </form>

    <div class="account-actions">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="index.php?action=dashboard" class="text-link">Dashboard</a>
            <a href="index.php?action=notifications" class="text-link">Notifications<?php if (isset($notificationCount) && $notificationCount > 0): ?> (<?= (int)$notificationCount; ?>)<?php endif; ?></a>
            <a href="index.php?action=profile" class="text-link">Profile</a>
            <form action="index.php?action=logout" method="POST" class="logout-form">
                <?= csrf_field(); ?>
                <button type="submit" class="text-link">Log out</button>
            </form>
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
