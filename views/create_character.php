<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_login();

$activeNav = 'create_character';

$classes = $pdo->query("SELECT class_id, class_name FROM classes")->fetchAll();
$races   = $pdo->query("SELECT race_id, race_name FROM races")->fetchAll();
$jobs    = $pdo->query("SELECT job_id, job_name FROM jobs")->fetchAll();

require_once __DIR__ . '/../partials/header.php';
?>

<div class="dnd-builder-container">
    <div class="top-bar">
        <a href="/index.php" class="back-link">&larr; BACK TO CHARACTERS</a>
        <div class="subtitle">&mdash; FORGE YOUR LEGEND &mdash;</div>
        <h1 class="page-title">CREATE CHARACTER</h1>
        
        <div class="wizard-steps">
            <span class="step active">RACE & CLASS</span>
            <span class="step">BACKGROUND</span>
            <span class="step">ABILITY SCORES</span>
            <span class="step">PERSONALITY</span>
            <span class="step-count">Step 1 of 4</span>
        </div>
    </div>

    <form action="../includes/create_character_handler.php" method="POST" class="builder-layout">

        <input type="hidden" name="character_class_id" id="selected_class_id" required>
        <input type="hidden" name="character_race_id" id="selected_race_id" required>
        <input type="hidden" name="character_job_id" id="selected_job_id" value="<?= $jobs[0]['job_id'] ?? 1 ?>">
        <input type="hidden" name="hp_max" value="10">

        <div class="selection-area">
            <div class="form-section">
                <label class="section-label" for="character_name">CHARACTER NAME</label>
                <input type="text" id="character_name" name="character_name" class="dnd-input" placeholder="Teemu-Tiikeri" maxlength="50" required autocomplete="off">
            </div>

            <div class="form-section">
                <label class="section-label">CLASS</label>
                <div class="card-grid">
                    <?php foreach ($classes as $class): ?>
                        <div class="dnd-card class-card" data-id="<?= $class['class_id'] ?>" data-name="<?= htmlspecialchars($class['class_name']) ?>">
                            <div class="card-title"><?= htmlspecialchars($class['class_name']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-section">
                <label class="section-label">RACE</label>
                <div class="card-grid">
                    <?php foreach ($races as $race): ?>
                        <div class="dnd-card race-card" data-id="<?= $race['race_id'] ?>" data-name="<?= htmlspecialchars($race['race_name']) ?>">
                            <div class="card-title"><?= htmlspecialchars($race['race_name']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="submit-btn">SAVE CHARACTER</button>
        </div>

        <div class="summary-area">
            <div class="summary-card">
                <h3 class="summary-title">SUMMARY</h3>
                <div class="summary-header">
                    <div class="summary-icon">&#128293;</div>
                    <div>
                        <div class="summary-name" id="summary-name-display">UNNAMED</div>
                        <div class="summary-sub" id="summary-sub-display">-</div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>

document.addEventListener('DOMContentLoaded', () => {
    const nameInput = document.getElementById('character_name');
    const summaryName = document.getElementById('summary-name-display');
    const summarySub = document.getElementById('summary-sub-display');
    
    let selectedRace = '';
    let selectedClass = '';

    nameInput.addEventListener('input', (e) => {
        summaryName.textContent = e.target.value.trim() || 'UNNAMED';
    });

    document.querySelectorAll('.class-card').forEach(card => {
        card.addEventListener('click', () => {
            document.querySelectorAll('.class-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            document.getElementById('selected_class_id').value = card.dataset.id;
            selectedClass = card.dataset.name;
            updateSub();
        });
    });

    document.querySelectorAll('.race-card').forEach(card => {
        card.addEventListener('click', () => {
            document.querySelectorAll('.race-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            document.getElementById('selected_race_id').value = card.dataset.id;
            selectedRace = card.dataset.name;
            updateSub();
        });
    });

    function updateSub() {
        summarySub.textContent = `${selectedRace} ${selectedClass}`.trim() || '-';
    }
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>