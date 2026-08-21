<?php 
$pageTitle = "Hahmon Tiedot - Roolipelisovellus";
require __DIR__ . '/partials/head.php'; 
?>


    <h1><?= htmlspecialchars($character['character_name']); ?></h1>
    <p>Taso: <?= $character['level']; ?> | Rotu: <?= $character['race_name']; ?> | Luokka: <?= $character['class_name']; ?> | Ammatti: <?= $character['job_name']; ?></p>
    <p>Kampanja: <?= $character['campaign_name'] ? htmlspecialchars($character['campaign_name']) : "Ei kampanjassa"; ?></p>

    <hr>

    <h3>HP-Hallinta (Pelinäkymä)</h3>
    <form action="index.php?action=character_update_hp" method="POST">
        <input type="hidden" name="character_id" value="<?= $character['character_id']; ?>">
        <label>Nykyinen HP: 
            <input type="number" name="hp_current" value="<?= $character['hp_current']; ?>" max="<?= $character['hp_max']; ?>">
        </label>
        / <?= $character['hp_max']; ?>
        <button type="submit">Päivitä HP</button>
    </form>

    <hr>

    <?php if (!$character['campaign_id']): ?>
        <h3>Liity kampanjaan</h3>
        <form action="index.php?action=character_join_campaign" method="POST">
            <input type="hidden" name="character_id" value="<?= $character['character_id']; ?>">
            <label>Syötä kutsukoodi: <input type="text" name="invite_code" required></label>
            <button type="submit">Liity</button>
        </form>
    <?php endif; ?>

    <br>
    <a href="index.php?action=dashboard">Palaa päänäkymään</a>



<?php require __DIR__ . '/partials/footer.php'; ?>