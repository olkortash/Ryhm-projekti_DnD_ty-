<?php
/*
 * Staattinen ohjesivu. Sisällysluettelon ankkurit vastaavat ohjeosioiden id-attribuutteja.
 */
$pageTitle = 'Help & Guide - Masters';
require __DIR__ . '/partials/head.php';
?>

<main class="help-page">
    <header class="help-hero">
        <p class="eyebrow">THE GAMEMASTER'S HANDBOOK</p>
        <h1>How to use Masters</h1>
        <p>Everything you need to create characters, organize campaigns, and keep your tabletop adventure moving.</p>
    </header>

    <div class="help-layout">
        <aside class="help-index" aria-label="Guide sections">
            <p class="eyebrow">ON THIS PAGE</p>
            <a href="#getting-started">Getting started</a>
            <a href="#characters">Characters</a>
            <a href="#campaigns">Campaigns</a>
            <a href="#game-master-tools">Game Master tools</a>
            <a href="#notifications-search">Notifications &amp; search</a>
        </aside>

        <div class="help-content">
            <section class="help-section" id="getting-started">
                <p class="eyebrow">01 / GETTING STARTED</p>
                <h2>Begin your adventure</h2>
                <p>Browse public campaigns from the home page, or create an account to manage your own characters and campaigns.</p>
                <ol class="help-steps">
                    <li><strong>Register.</strong> Create an account with your username, email address, and password.</li>
                    <li><strong>Open your dashboard.</strong> This is your personal hub for characters and campaigns.</li>
                    <li><strong>Choose your role.</strong> Create a character as a player, or create a campaign as a Game Master.</li>
                </ol>
            </section>

            <section class="help-section" id="characters">
                <p class="eyebrow">02 / CHARACTERS</p>
                <h2>Create and manage characters</h2>
                <p>Use <strong>Create Character</strong> to choose a name, portrait, race, class, job, hit points, and ability scores. Maximum HP and the seven ability scores share a 47-point budget; each ability must be between 1 and 25.</p>
                <p>Open your character from the dashboard to replace its portrait, update hit points and ability scores, maintain equipment and special skills, or join a campaign with an invite code. A character can belong to one campaign at a time.</p>
            </section>

            <section class="help-section" id="campaigns">
                <p class="eyebrow">03 / CAMPAIGNS</p>
                <h2>Find or create a campaign</h2>
                <p>Campaigns appear on the home page and in search. Signed-in users can open a campaign and join it with one of their available characters. Member rosters, announcements, and session notes are visible only to campaign members and the Game Master.</p>
                <p>You can also join from a character page with the campaign's invite code. Leave a campaign from the dashboard when you want to use that character elsewhere.</p>
            </section>

            <section class="help-section" id="game-master-tools">
                <p class="eyebrow">04 / GAME MASTER TOOLS</p>
                <h2>Run the table</h2>
                <p>The campaign creator is its only Game Master. Game Masters can edit campaigns, add or remove player characters, level characters up to level 10, and publish campaign announcements.</p>
                <p>Session records include a date, title, summary, and attendance list. Saving announcements and session notes notifies campaign members.</p>
                <p>Your profile shows account information and a quick summary of your characters, created campaigns, and joined campaigns.</p>
            </section>

            <section class="help-section" id="notifications-search">
                <p class="eyebrow">05 / NOTIFICATIONS &amp; SEARCH</p>
                <h2>Keep up with the party</h2>
                <p>Use the header search to find users by username and campaigns by name or description. Search results never expose email addresses or invite codes.</p>
                <p>The Notifications page collects campaign membership changes, announcements, and new session notes. Notifications can be marked read individually or all at once.</p>
            </section>

            <section class="help-callout">
                <strong>Need a place to start?</strong>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="manage-link" href="index.php?action=dashboard">Open your dashboard <span aria-hidden="true">→</span></a>
                <?php else: ?>
                    <a class="manage-link" href="index.php?action=register">Create your account <span aria-hidden="true">→</span></a>
                <?php endif; ?>
            </section>
        </div>
    </div>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
