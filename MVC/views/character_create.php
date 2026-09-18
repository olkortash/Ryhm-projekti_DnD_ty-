<?php 
$pageTitle = "Luo uusi hahmo - Roolipelisovellus";
require __DIR__ . '/partials/head.php'; 
?>

<div class="character-creator">
    <div class="creator-header">
        <h1>Create Character</h1>
    </div>

    <form action="index.php?action=character_create" method="POST">
        <div class="creator-layout">
            <!-- Left: Form -->
            <div class="creator-form">
                <!-- Character Name -->
                <div class="form-section">
                    <h3>Character Name</h3>
                    <div class="form-group">
                        <input type="text" name="character_name" placeholder="T.Halme" required>
                    </div>
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

                <!-- Class Selection -->
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
                        <span class="stat-label">Level</span>
                        <span class="stat-value" id="statLevel">1</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Race</span>
                        <span class="stat-value" id="statRace">—</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Class</span>
                        <span class="stat-value" id="statClass">—</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Job</span>
                        <span class="stat-value" id="statJob">—</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Hit Points</span>
                        <span class="stat-value" id="statHP">10</span>
                    </div>
                    <div class= "stat-row">
                        <span class="stat-label">Agility</span>
                        <span class="stat-value" id="statAGI">0</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Strength</span>
                        <span class="stat-value" id="statSTR">0</span> 
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Dexterity</span>
                        <span class="stat-value" id="statDEX">0</span>
                    </div> 
                    <div class="stat-row">
                        <span class="stat-label">Wisdom</span>
                        <span class="stat-value" id="statWIS">0</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Charm</span>
                        <span class="stat-value" id="statCHA">0</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Constitution</span>
                        <span class="stat-value" id="statCON">0</span>
                    </div> 
                    <div class="stat-row">
                        <span class="stat-label">Intelligence</span>
                        <span class="stat-value" id="statINT">0</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="js/character-creator.js"></script>

<?php require __DIR__ . '/partials/footer.php'; ?>