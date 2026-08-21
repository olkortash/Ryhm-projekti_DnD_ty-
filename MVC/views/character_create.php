<?php 
$pageTitle = "Luo uusi hahmo - Roolipelisovellus";
require __DIR__ . '/partials/head.php'; 
?>


    <h2>Luo uusi hahmo</h2>
    <form action="index.php?action=character_create" method="POST">
        <label>Hahmon nimi: <input type="text" name="character_name" required></label><br>
        
        <label>Rotu:
            <select name="character_race_id" required>
                <?php foreach ($races as $race): ?>
                    <option value="<?= $race['race_id']; ?>"><?= htmlspecialchars($race['race_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>

        <label>Luokka (Class):
            <select name="character_class_id" required>
                <?php foreach ($classes as $cls): ?>
                    <option value="<?= $cls['class_id']; ?>"><?= htmlspecialchars($cls['class_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>

        <label>Ammatti (Job):
            <select name="character_job_id" required>
                <?php foreach ($jobs as $job): ?>
                    <option value="<?= $job['job_id']; ?>"><?= htmlspecialchars($job['job_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>

        <label>Taso (Level): <input type="number" name="level" value="1" min="1"></label><br>
        <label>Maksimi HP: <input type="number" name="hp_max" value="10" min="1" required></label><br>

        <button type="submit">Tallenna hahmo</button>
    </form>
    <a href="index.php?action=dashboard">Palaa takaisin</a>



<?php require __DIR__ . '/partials/footer.php'; ?>