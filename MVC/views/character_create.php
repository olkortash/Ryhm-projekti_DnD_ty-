<?php 
$pageTitle = "Create new character - Roleplay App";
require __DIR__ . '/partials/head.php'; 
?>

<div class="character-creator">
    <div class="creator-header">
        <h1>Create Character</h1>
    </div>

    <form action="index.php?action=character_create" method="POST" enctype="multipart/form-data">
        <div class="creator-layout">
            <div class="creator-form">
                <div class="form-section">
                    <h3>Character Name</h3>
                    <div class="form-group">
                        <input type="text" name="character_name" placeholder="T.Halme" required>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Profile Image</h3>
                    <div class="form-group">
                        <label for="character-image">Upload a portrait (optional)</label>
                        <input id="character-image" type="file" name="character_image" accept="image/jpeg,image/png,image/webp,image/gif">
                        <small class="form-help">JPG, PNG, WEBP, or GIF. Maximum size 5 MB.</small>
                    </div>
                    <?php if (!empty($error)): ?>
                        <p class="form-error"><?= htmlspecialchars($error); ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-section">
                    <h3>Race</h3>
                    <div class="options-grid" id="raceGrid">
                        <?php foreach ($races as $race): ?>
                            <label class="option-card">
                                <input type="radio" name="character_race_id" value="<?= $race['race_id']; ?>" 
                                       data-race="<?= htmlspecialchars($race['race_name']); ?>"
                                       <?= ($race['race_id'] == 1 ? 'checked' : ''); ?>>
                                <div class="option-label"><?= htmlspecialchars($race['race_name']); ?></div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Class</h3>
                    <div class="options-grid" id="classGrid">
                        <?php foreach ($classes as $cls): ?>
                            <label class="option-card">
                                <input type="radio" name="character_class_id" value="<?= $cls['class_id']; ?>" 
                                       data-class="<?= htmlspecialchars($cls['class_name']); ?>" 
                                       <?= ($cls['class_id'] == 1 ? 'checked' : ''); ?>>
                                <div class="option-label"><?= htmlspecialchars($cls['class_name']); ?></div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>


                <div class="form-section">
                    <h3>Job</h3>
                    <div class="form-group">
                        <select name="character_job_id" required id="jobSelect" class="dark-select">
                            <option value="">Choose a profession...</option>
                            <?php foreach ($jobs as $job): ?>
                                <option value="<?= $job['job_id']; ?>" 
                                        data-job="<?= htmlspecialchars($job['job_name']); ?>">
                                    <?= htmlspecialchars($job['job_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Stats</h3>
                    <div class="stats-points">
                        <span>Points remaining</span>
                        <strong id="pointsLeft">40</strong>
                    </div>
                        <div class="form-group">
                        <label>Hit Points (HP)</label>
                        <div class="stat-stepper">
                            <button type="button" class="stat-button" data-target="hp_max" data-action="decrease" aria-label="Decrease hit points">−</button>
                            <input type="number" name="hp_max" value="15" min="15" max="25" class="stat-input hp-input" data-stat="hp_max" required>
                            <button type="button" class="stat-button" data-target="hp_max" data-action="increase" aria-label="Increase hit points">+</button>
                        </div>
                    <div class="form-group">
                        <label>Agility (AGI)</label>
                        <div class="stat-stepper">
                            <button type="button" class="stat-button" data-stat="agi" data-action="decrease" aria-label="Decrease agility">−</button>
                            <input type="number" name="agi" value="1" min="1" max="25" class="stat-input" data-stat="agi" required>
                            <button type="button" class="stat-button" data-stat="agi" data-action="increase" aria-label="Increase agility">+</button>
                        </div>
                    </div>
                    </div>
                    <div class="form-group">
                        <label>Strength (STR)</label>
                        <div class="stat-stepper">
                            <button type="button" class="stat-button" data-stat="str" data-action="decrease" aria-label="Decrease strength">−</button>
                            <input type="number" name="str" value="1" min="1" max="25" class="stat-input" data-stat="str" required>
                            <button type="button" class="stat-button" data-stat="str" data-action="increase" aria-label="Increase strength">+</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Dexterity (DEX)</label>
                        <div class="stat-stepper">
                            <button type="button" class="stat-button" data-stat="dex" data-action="decrease" aria-label="Decrease dexterity">−</button>
                            <input type="number" name="dex" value="1" min="1" max="25" class="stat-input" data-stat="dex" required>
                            <button type="button" class="stat-button" data-stat="dex" data-action="increase" aria-label="Increase dexterity">+</button>
                        </div>
                    </div>
                     <div class="form-group">
                        <label>Wisdom (WIS)</label>
                        <div class="stat-stepper">
                            <button type="button" class="stat-button" data-stat="wis" data-action="decrease" aria-label="Decrease wisdom">−</button>
                            <input type="number" name="wis" value="1" min="1" max="25" class="stat-input" data-stat="wis" required>
                            <button type="button" class="stat-button" data-stat="wis" data-action="increase" aria-label="Increase wisdom">+</button>
                        </div>
                    </div>
                     <div class="form-group">
                        <label>Charm (CHA)</label>
                        <div class="stat-stepper">
                            <button type="button" class="stat-button" data-stat="cha" data-action="decrease" aria-label="Decrease charm">−</button>
                            <input type="number" name="cha" value="1" min="1" max="25" class="stat-input" data-stat="cha" required>
                            <button type="button" class="stat-button" data-stat="cha" data-action="increase" aria-label="Increase charm">+</button>
                        </div>
                    </div>
                     <div class="form-group">
                        <label>Constitution (CON)</label>
                        <div class="stat-stepper">
                            <button type="button" class="stat-button" data-stat="con" data-action="decrease" aria-label="Decrease constitution">−</button>
                            <input type="number" name="con" value="1" min="1" max="25" class="stat-input" data-stat="con" required>
                            <button type="button" class="stat-button" data-stat="con" data-action="increase" aria-label="Increase constitution">+</button>
                        </div>
                    </div>
                     <div class="form-group">
                        <label>Intelligence (INT)</label>
                        <div class="stat-stepper">
                            <button type="button" class="stat-button" data-stat="int" data-action="decrease" aria-label="Decrease intelligence">−</button>
                            <input type="number" name="int" value="1" min="1" max="25" class="stat-input" data-stat="int" required>
                            <button type="button" class="stat-button" data-stat="int" data-action="increase" aria-label="Increase intelligence">+</button>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="level" value="1">

                <div class="form-actions">
                    <a href="index.php?action=dashboard" class="btn">← Back to Characters</a>
                    <button type="submit" class="btn btn-primary">Create Character</button>
                </div>
            </div>

            <div class="summary-panel">
                <h3>Summary</h3>
                <div class="summary-name" id="summaryName">Unnamed</div>
                <div class="summary-subtitle" id="summarySubtitle">No class/race selected</div>

                <div class="summary-stats">
                    <div class="stat-row">
                        <span class="stat-label"><svg class="summary-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="m5 11 7-7 7 7M5 19l7-7 7 7"/></svg>Level</span>
                        <span class="stat-value" id="statLevel">1</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label"><svg class="summary-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M6 9 2 6l1 7 4 2m11-6 4-3-1 7-4 2M6 10c0-8 12-8 12 0v4c0 8-12 8-12 0Z"/><path d="M8 11h1m6 0h1m-6 6h4"/></svg>Race</span>
                        <span class="stat-value" id="statRace">—</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label"><svg class="summary-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="m7 17 4-14 6 4-4 1 4 9M7 17c-7 1-7 4 5 4s12-3 5-4M8 14h8"/></svg>Class</span>
                        <span class="stat-value" id="statClass">—</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label"><svg class="summary-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="m4 19 9-9 3 3-9 9-3-3ZM10 7l5-5 7 7-5 5-7-7Z"/></svg>Job</span>
                        <span class="stat-value" id="statJob">—</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label"><svg class="summary-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z"/></svg>Hit Points</span>
                        <span class="stat-value" id="statHP">10</span>
                    </div>
                    <div class= "stat-row">
                        <span class="stat-label"><svg class="summary-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="m4 5 8-1 1 9 3 3v4H4v-4l3-3-3-8ZM14 7l8-3-2 5-6 2m1 0 5-1-2 4-3 1"/></svg>Agility</span>
                        <span class="stat-value" id="statAGI">0</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label"><svg class="summary-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M7 12V6a2 2 0 0 1 4 0v5-7a2 2 0 0 1 4 0v7-5a2 2 0 0 1 4 0v7l-2 4v4H8v-4l-4-5V9a2 2 0 0 1 3 0m0 4h6v3"/></svg>Strength</span>
                        <span class="stat-value" id="statSTR">0</span> 
                    </div>
                    <div class="stat-row">
                        <span class="stat-label"><svg class="summary-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M8 13V6a1.5 1.5 0 0 1 3 0v6-8a1.5 1.5 0 0 1 3 0v8-6a1.5 1.5 0 0 1 3 0v7-4a1.5 1.5 0 0 1 3 0v6a8 8 0 0 1-15 4l-3-6a1.5 1.5 0 0 1 2.5-1.5L8 15"/></svg>Dexterity</span>
                        <span class="stat-value" id="statDEX">0</span>
                    </div> 
                    <div class="stat-row">
                        <span class="stat-label"><svg class="summary-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>Wisdom</span>
                        <span class="stat-value" id="statWIS">0</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label"><svg class="summary-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M12 2c1 7 3 9 10 10-7 1-9 3-10 10-1-7-3-9-10-10 7-1 9-3 10-10Z"/></svg>Charm</span>
                        <span class="stat-value" id="statCHA">0</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label"><svg class="summary-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="m12 3 8 3v6c0 5-5 8-8 10-3-2-8-5-8-10V6l8-3Z"/></svg>Constitution</span>
                        <span class="stat-value" id="statCON">0</span>
                    </div> 
                    <div class="stat-row">
                        <span class="stat-label"><svg class="summary-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M12 6C9 3 5 3 2 4v16c3-1 7-1 10 2 3-3 7-3 10-2V4c-3-1-7-1-10 2Zm0 0v16"/></svg>Intelligence</span>
                        <span class="stat-value" id="statINT">0</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="js/character-creator.js"></script>

<?php require __DIR__ . '/partials/footer.php'; ?>