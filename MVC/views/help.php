<?php
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
                <p>Use <strong>Create Character</strong> to build a playable character. Add the character's name, race, class, level, and ability scores. The creator updates the ability summary as you work.</p>
                <p>From the dashboard, open a character to review its details, update hit points during play, join a campaign, or leave a campaign.</p>
            </section>

            <section class="help-section" id="campaigns">
                <p class="eyebrow">03 / CAMPAIGNS</p>
                <h2>Find or create a campaign</h2>
                <p>Public campaigns appear on the home page. Open a campaign to see its description, members, and session notes.</p>
                <p>To join a campaign, sign in, create or select a character, and use the campaign's invite code when prompted. Your character will then appear among the campaign members.</p>
            </section>

            <section class="help-section" id="game-master-tools">
                <p class="eyebrow">04 / GAME MASTER TOOLS</p>
                <h2>Run the table</h2>
                <p>Game Masters can create campaigns from the dashboard, manage campaign members, and keep a record of each session. Add a session title, date, summary, and participants so the story remains easy to follow between games.</p>
                <p>Your profile shows account information and a quick summary of your characters, created campaigns, and joined campaigns.</p>
            </section>

            <section class="help-callout">
                <strong>Need a place to start?</strong>
                <a class="manage-link" href="index.php?action=register">Create your account <span aria-hidden="true">→</span></a>
            </section>
        </div>
    </div>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>