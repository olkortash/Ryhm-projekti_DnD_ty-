<?php
$pageTitle = 'Sitemap - Masters';
require __DIR__ . '/partials/head.php';
?>

<main class="help-page sitemap-page">
    <header class="help-hero">
        <p class="eyebrow">MASTERS / NAVIGATION</p>
        <h1>Sitemap</h1>
        <p>Quick access to the main areas of the Masters platform.</p>
    </header>

    <div class="sitemap-grid">
        <section class="sitemap-group">
            <p class="eyebrow">PUBLIC</p>
            <h2>Explore</h2>
            <a href="index.php?action=landing">Home and public campaigns <span aria-hidden="true">→</span></a>
            <a href="index.php?action=help">Help &amp; guide <span aria-hidden="true">→</span></a>
            <a href="index.php?action=sitemap">Sitemap <span aria-hidden="true">→</span></a>
        </section>

        <section class="sitemap-group">
            <p class="eyebrow">ACCOUNT</p>
            <h2>Your table</h2>
            <a href="index.php?action=login">Sign in <span aria-hidden="true">→</span></a>
            <a href="index.php?action=register">Create an account <span aria-hidden="true">→</span></a>
            <a href="index.php?action=dashboard">Dashboard <span aria-hidden="true">→</span></a>
            <a href="index.php?action=profile">Profile <span aria-hidden="true">→</span></a>
        </section>

        <section class="sitemap-group">
            <p class="eyebrow">TOOLS</p>
            <h2>Build the story</h2>
            <a href="index.php?action=character_create">Create a character <span aria-hidden="true">→</span></a>
            <a href="index.php?action=dashboard">Create a campaign <span aria-hidden="true">→</span></a>
        </section>
    </div>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>