<?php 
$pageTitle = "Hahmon Tiedot - Roolipelisovellus";
require __DIR__ . '/partials/head.php'; 
?>


<main class="character-page">
    <div class="character-page-header">
        <a class="manage-link" href="index.php?action=dashboard">← Dashboard</a>
        <p class="eyebrow">CHARACTER PROFILE</p>
        <h1><?= htmlspecialchars($character['character_name']); ?></h1>
        <p class="character-subtitle">Level <?= $character['level']; ?> · <?= htmlspecialchars($character['race_name']); ?> <?= htmlspecialchars($character['class_name']); ?></p>
    </div>

    <div class="character-profile-layout">
        <section class="character-portrait-panel" aria-label="Character profile image">
            <?php if (!empty($character['character_img_id'])): ?>
                <div class="character-portrait-frame">
                    <img
                        src="index.php?action=character_image&amp;id=<?= (int) $character['character_id']; ?>"
                        alt="Profile portrait of <?= htmlspecialchars($character['character_name']); ?>"
                    >
                </div>
            <?php else: ?>
                <div class="character-portrait-placeholder" role="img" aria-label="Character profile image placeholder">
                    <span aria-hidden="true">✦</span>
                    <p>Profile image</p>
                    <small>No image yet</small>
                </div>
            <?php endif; ?>

            <form class="character-image-form" action="index.php?action=character_update_image" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="character_id" value="<?= (int) $character['character_id']; ?>">
                <label for="replace-character-image"><?= !empty($character['character_img_id']) ? 'Replace image' : 'Upload image'; ?></label>
                <input id="replace-character-image" type="file" name="character_image" accept="image/jpeg,image/png,image/webp,image/gif">
                <?php if (!empty($character['character_img_id'])): ?>
                    <label class="image-remove-option">
                        <input type="checkbox" name="remove_image" value="1">
                        Remove current image
                    </label>
                <?php endif; ?>
                <?php if (($_GET['error'] ?? '') === 'image'): ?>
                    <p class="form-error">Please choose a valid image up to 5 MB in size.</p>
                <?php endif; ?>
                <button type="submit" class="btn btn-secondary">Save image</button>
            </form>
        </section>

        <section class="character-details-panel" aria-label="Character details">
            <div class="character-stat-grid">
                <div class="character-stat"><span class="stat-label">Race</span><strong><?= htmlspecialchars($character['race_name']); ?></strong></div>
                <div class="character-stat"><span class="stat-label">Class</span><strong><?= htmlspecialchars($character['class_name']); ?></strong></div>
                <div class="character-stat"><span class="stat-label">Job</span><strong><?= htmlspecialchars($character['job_name']); ?></strong></div>
                <div class="character-stat"><span class="stat-label">Campaign</span><strong><?= $character['campaign_name'] ? htmlspecialchars($character['campaign_name']) : 'No campaign'; ?></strong></div>
            </div>

            <div class="character-ability-section">
                <p class="eyebrow">ABILITY SCORES</p>
                <div class="ability-score-grid">
                    <div class="ability-score">
                        <span>AGI</span>
                        <strong><?= htmlspecialchars($character['agi'] ?? 0); ?></strong>
                    </div>
                    <div class="ability-score">
                        <span>STR</span>
                        <strong><?= htmlspecialchars($character['str'] ?? 0); ?></strong>
                    </div>
                    <div class="ability-score">
                        <span>DEX</span>
                        <strong><?= htmlspecialchars($character['dex'] ?? 0); ?></strong>
                    </div>
                    <div class="ability-score">
                        <span>WIS</span>
                        <strong><?= htmlspecialchars($character['wis'] ?? 0); ?></strong>
                    </div>
                    <div class="ability-score">
                        <span>CHA</span>
                        <strong><?= htmlspecialchars($character['cha'] ?? 0); ?></strong>
                    </div>
                    <div class="ability-score">
                        <span>CON</span>
                        <strong><?= htmlspecialchars($character['con'] ?? 0); ?></strong>
                    </div>
                    <div class="ability-score">
                        <span>INT</span>
                        <strong><?= htmlspecialchars($character['int'] ?? 0); ?></strong>
                    </div>
                </div>
            </div>

            <div class="character-action-section">
                <p class="eyebrow">PLAYING STATUS</p>

                <h2>Hit Points</h2>
                <form class="character-hp-form" action="index.php?action=character_update_hp" method="POST">
                    <input type="hidden" name="character_id" value="<?= $character['character_id']; ?>">
                    <label for="hp-current">Current HP</label>
                    <div class="hp-input-row">
                        <input id="hp-current" type="number" name="hp_current" value="<?= $character['hp_current']; ?>" min="0" max="<?= $character['hp_max']; ?>">
                        <span>/ <?= $character['hp_max']; ?></span>
                        <button type="submit" class="btn btn-primary">Update HP</button>
                    </div>
                </form>
            </div>

            <?php if (!$character['campaign_id']): ?>
                <div class="character-action-section">
                    <p class="eyebrow">CAMPAIGN</p>
                    <h2>Join a campaign</h2>
                    <form class="character-hp-form" action="index.php?action=character_join_campaign" method="POST">
                        <input type="hidden" name="character_id" value="<?= $character['character_id']; ?>">
                        <label for="invite-code">Invite code</label>
                        <div class="hp-input-row">
                            <input id="invite-code" type="text" name="invite_code" required>
                            <button type="submit" class="btn btn-secondary">Join</button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <div class="character-danger-zone">
                <form action="index.php?action=character_delete" method="POST" onsubmit="return confirm('Haluatko varmasti poistaa tämän hahmon? Tätä toimintoa ei voi perua.');">
                    <input type="hidden" name="character_id" value="<?= $character['character_id']; ?>">
                    <button type="submit" class="btn btn-danger">Delete character</button>
                </form>
            </div>
        </section>
    </div>
</main>



<?php require __DIR__ . '/partials/footer.php'; ?>