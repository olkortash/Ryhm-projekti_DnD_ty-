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
                    <h3>Job</h3>
                    <div class="form-group">
                        <select name="character_job_id" required id="jobSelect">
                            <option value="">Valitse ammatti...</option>
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
                    <div class="form-group">
                        <label>Level</label>
                        <input type="number" name="level" value="1" min="1" max="20">
                    </div>
                    <div class="form-group">
                        <label>Hit Points (HP)</label>
                        <input type="number" name="hp_max" value="10" min="1" required>
                    </div>
                </div>

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
                        <span class="stat-label">Class</span>
                        <span class="stat-value" id="statClass">—</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Race</span>
                        <span class="stat-value" id="statRace">—</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Job</span>
                        <span class="stat-value" id="statJob">—</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Hit Points</span>
                        <span class="stat-value" id="statHP">10</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="js/character-creator.js"></script>

<?php require __DIR__ . '/partials/footer.php'; ?>