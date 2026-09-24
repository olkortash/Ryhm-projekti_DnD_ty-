<?php 
$pageTitle = "Character information - Roleplay App";
require __DIR__ . '/partials/head.php'; 
?>

<main class="character-page">
    <svg class="character-icon-sprite" aria-hidden="true" focusable="false">
        <symbol id="profile-icon-race" viewBox="0 0 24 24"><path d="M6 9 2 6l1 7 4 2m11-6 4-3-1 7-4 2M6 10c0-8 12-8 12 0v4c0 8-12 8-12 0Z"/><path d="M8 11h1m6 0h1m-6 6h4"/></symbol>
        <symbol id="profile-icon-class" viewBox="0 0 24 24"><path d="m7 17 4-14 6 4-4 1 4 9M7 17c-7 1-7 4 5 4s12-3 5-4M8 14h8"/></symbol>
        <symbol id="profile-icon-job" viewBox="0 0 24 24"><path d="m4 19 9-9 3 3-9 9-3-3ZM10 7l5-5 7 7-5 5-7-7Z"/></symbol>
        <symbol id="profile-icon-campaign" viewBox="0 0 24 24"><path d="M5 3v18M6 4h12l-3 4 3 4H6"/></symbol>
        <symbol id="profile-icon-user" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/></symbol>
        <symbol id="profile-icon-hp" viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z"/></symbol>
        <symbol id="profile-icon-agi" viewBox="0 0 24 24"><path d="m4 5 8-1 1 9 3 3v4H4v-4l3-3-3-8ZM14 7l8-3-2 5-6 2m1 0 5-1-2 4-3 1"/></symbol>
        <symbol id="profile-icon-str" viewBox="0 0 24 24"><path d="M7 12V6a2 2 0 0 1 4 0v5-7a2 2 0 0 1 4 0v7-5a2 2 0 0 1 4 0v7l-2 4v4H8v-4l-4-5V9a2 2 0 0 1 3 0m0 4h6v3"/></symbol>
        <symbol id="profile-icon-dex" viewBox="0 0 24 24"><path d="M8 13V6a1.5 1.5 0 0 1 3 0v6-8a1.5 1.5 0 0 1 3 0v8-6a1.5 1.5 0 0 1 3 0v7-4a1.5 1.5 0 0 1 3 0v6a8 8 0 0 1-15 4l-3-6a1.5 1.5 0 0 1 2.5-1.5L8 15"/></symbol>
        <symbol id="profile-icon-wis" viewBox="0 0 24 24"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></symbol>
        <symbol id="profile-icon-cha" viewBox="0 0 24 24"><path d="M12 2c1 7 3 9 10 10-7 1-9 3-10 10-1-7-3-9-10-10 7-1 9-3 10-10Z"/></symbol>
        <symbol id="profile-icon-con" viewBox="0 0 24 24"><path d="m12 3 8 3v6c0 5-5 8-8 10-3-2-8-5-8-10V6l8-3Z"/></symbol>
        <symbol id="profile-icon-int" viewBox="0 0 24 24"><path d="M12 6C9 3 5 3 2 4v16c3-1 7-1 10 2 3-3 7-3 10-2V4c-3-1-7-1-10 2Zm0 0v16"/></symbol>
    </svg>
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

            <?php if ($isOwner): ?>
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
            <?php endif; ?>
        </section>
                   
        
        <section class="character-details-panel" aria-label="Character details">
            <div class="character-stat-grid">
                <div class="character-stat"><span class="stat-label"><svg class="profile-icon" aria-hidden="true"><use href="#profile-icon-race"/></svg>Race</span><strong><?= htmlspecialchars($character['race_name']); ?></strong></div>
                <div class="character-stat"><span class="stat-label"><svg class="profile-icon" aria-hidden="true"><use href="#profile-icon-class"/></svg>Class</span><strong><?= htmlspecialchars($character['class_name']); ?></strong></div>
                <div class="character-stat"><span class="stat-label"><svg class="profile-icon" aria-hidden="true"><use href="#profile-icon-job"/></svg>Job</span><strong><?= htmlspecialchars($character['job_name']); ?></strong></div>
                <div class="character-stat"><span class="stat-label"><svg class="profile-icon" aria-hidden="true"><use href="#profile-icon-campaign"/></svg>Campaign</span><strong><?= $character['campaign_name'] ? htmlspecialchars($character['campaign_name']) : 'No campaign'; ?></strong></div>
                <div class="character-stat"><span class="stat-label"><svg class="profile-icon" aria-hidden="true"><use href="#profile-icon-user"/></svg>Created by</span><strong><?= htmlspecialchars($character['creator_username']); ?></strong></div>
            </div>

            <div class="character-ability-section">
                <p class="eyebrow">ABILITY SCORES</p>
                <div class="ability-score-grid">
                    <div class="ability-score">
                        <svg class="ability-icon" aria-hidden="true"><use href="#profile-icon-agi"/></svg>
                        <span>AGI</span>
                        <strong><?= htmlspecialchars($character['agility'] ?? 0); ?></strong>
                    </div>
                    <div class="ability-score">
                        <svg class="ability-icon" aria-hidden="true"><use href="#profile-icon-str"/></svg>
                        <span>STR</span>
                        <strong><?= htmlspecialchars($character['strength'] ?? 0); ?></strong>
                    </div>
                    <div class="ability-score">
                        <svg class="ability-icon" aria-hidden="true"><use href="#profile-icon-dex"/></svg>
                        <span>DEX</span>
                        <strong><?= htmlspecialchars($character['dexterity'] ?? 0); ?></strong>
                    </div>
                    <div class="ability-score">
                        <svg class="ability-icon" aria-hidden="true"><use href="#profile-icon-wis"/></svg>
                        <span>WIS</span>
                        <strong><?= htmlspecialchars($character['wisdom'] ?? 0); ?></strong>
                    </div>
                    <div class="ability-score">
                        <svg class="ability-icon" aria-hidden="true"><use href="#profile-icon-cha"/></svg>
                        <span>CHA</span>
                        <strong><?= htmlspecialchars($character['charisma'] ?? 0); ?></strong>
                    </div>
                    <div class="ability-score">
                        <svg class="ability-icon" aria-hidden="true"><use href="#profile-icon-con"/></svg>
                        <span>CON</span>
                        <strong><?= htmlspecialchars($character['constitution'] ?? 0); ?></strong>
                    </div>
                    <div class="ability-score">
                        <svg class="ability-icon" aria-hidden="true"><use href="#profile-icon-int"/></svg>
                        <span>INT</span>
                        <strong><?= htmlspecialchars($character['intelligence'] ?? 0); ?></strong>
                    </div>
                </div>

                <?php if ($isOwner): ?>
                <form class="character-hp-form" action="index.php?action=character_update_abilities" method="POST">
                    <input type="hidden" name="character_id" value="<?= (int) $character['character_id']; ?>">
                    <div class="ability-score-grid ability-score-edit-grid">
                        <label class="ability-score-field">
                            <span>AGI</span>
                            <input type="number" name="agi" value="<?= (int) ($character['agility'] ?? 0); ?>" min="0" max="99">
                        </label>
                        <label class="ability-score-field">
                            <span>STR</span>
                            <input type="number" name="str" value="<?= (int) ($character['strength'] ?? 0); ?>" min="0" max="99">
                        </label>
                        <label class="ability-score-field">
                            <span>DEX</span>
                            <input type="number" name="dex" value="<?= (int) ($character['dexterity'] ?? 0); ?>" min="0" max="99">
                        </label>
                        <label class="ability-score-field">
                            <span>WIS</span>
                            <input type="number" name="wis" value="<?= (int) ($character['wisdom'] ?? 0); ?>" min="0" max="99">
                        </label>
                        <label class="ability-score-field">
                            <span>CHA</span>
                            <input type="number" name="cha" value="<?= (int) ($character['charisma'] ?? 0); ?>" min="0" max="99">
                        </label>
                        <label class="ability-score-field">
                            <span>CON</span>
                            <input type="number" name="con" value="<?= (int) ($character['constitution'] ?? 0); ?>" min="0" max="99">
                        </label>
                        <label class="ability-score-field">
                            <span>INT</span>
                            <input type="number" name="int" value="<?= (int) ($character['intelligence'] ?? 0); ?>" min="0" max="99">
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary">Update ability scores</button>
                </form>
                <?php endif; ?>
            </div>

            <div class="character-action-section">
                <p class="eyebrow">EQUIPMENT & SPECIAL SKILLS</p>
                <?php if ($isOwner): ?>
                <form class="character-hp-form" action="index.php?action=character_update_details" method="POST">
                    <input type="hidden" name="character_id" value="<?= (int) $character['character_id']; ?>">
                    <label for="equipment-text">Equipment</label>
                    <textarea id="equipment-text" name="equipment" rows="5" placeholder="List equipment and items acquired during the campaign..."><?= htmlspecialchars($character['equipment'] ?? ''); ?></textarea>
                    <label for="skills-text">Additional skills</label>
                    <textarea id="skills-text" name="skills" rows="5" placeholder="Add learned abilities, talents, or custom skills..."><?= htmlspecialchars($character['additional_skills'] ?? ''); ?></textarea>
                    <button type="submit" class="btn btn-secondary">Save equipment and skills</button>
                </form>
                <?php else: ?>
                    <div class="character-note-box">
                        <h3>Equipment</h3>
                        <p><?= nl2br(htmlspecialchars($character['equipment'] ?? 'No equipment listed yet.')); ?></p>
                    </div>
                    <div class="character-note-box">
                        <h3>Additional skills</h3>
                        <p><?= nl2br(htmlspecialchars($character['additional_skills'] ?? 'No additional skills listed yet.')); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="character-action-section">
                <p class="eyebrow">PLAYING STATUS</p>

                <h2><svg class="profile-icon profile-icon-heading" aria-hidden="true"><use href="#profile-icon-hp"/></svg>Hit Points</h2>
                <?php if ($isOwner): ?>
                <form class="character-hp-form" action="index.php?action=character_update_hp" method="POST">
                    <input type="hidden" name="character_id" value="<?= $character['character_id']; ?>">
                    <label for="hp-current">Current HP</label>
                    <div class="hp-input-row">
                        <input id="hp-current" type="number" name="hp_current" value="<?= $character['hp_current']; ?>" min="0" max="<?= $character['hp_max']; ?>">
                        <span>/ <?= $character['hp_max']; ?></span>
                        <button type="submit" class="btn btn-primary">Update HP</button>
                    </div>
                </form>
                <?php else: ?>
                    <p><?= (int) $character['hp_current']; ?> / <?= (int) $character['hp_max']; ?></p>
                <?php endif; ?>
            </div>

            <?php if ($isOwner && !$character['campaign_id']): ?>
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

            <?php if ($isOwner): ?>
            <div class="character-danger-zone">
                <form action="index.php?action=character_delete" method="POST" onsubmit="return confirm('Haluatko varmasti poistaa tämän hahmon? Tätä toimintoa ei voi perua.');">
                    <input type="hidden" name="character_id" value="<?= $character['character_id']; ?>">
                    <button type="submit" class="btn btn-danger">Delete character</button>
                </form>
            </div>
            <?php endif; ?>
        </section>
    </div>
</main>



<?php require __DIR__ . '/partials/footer.php'; ?>
