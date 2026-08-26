<?php 
$pageTitle = "Dashboard - Roolipelisovellus";
require __DIR__ . '/partials/head.php'; 
?>


<h1>Tervetuloa, <?= htmlspecialchars($_SESSION['username']); ?>!</h1>

    <section>
        <h2>Omat Hahmot</h2>
        <a href="index.php?action=character_create">+ Luo uusi hahmo</a>
        <ul>
            <?php foreach ($characters as $char): ?>
                <li>
                    <a href="index.php?action=character_view&id=<?= $char['character_id']; ?>">
                        <strong><?= htmlspecialchars($char['character_name']); ?></strong> 
                        (Lvl <?= $char['level']; ?> <?= $char['race_name']; ?> <?= $char['class_name']; ?>)
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section>
        <h2>Omat Kampanjat (Pelinjohtaja)</h2>
        <form action="index.php?action=campaign_create" method="POST">
            <input type="text" name="campaign_name" placeholder="Kampanjan nimi" required>
            <input type="text" name="description" placeholder="Kuvaus">
            <button type="submit">Luo kampanja</button>
        </form>

        <ul>
            <?php foreach ($gmCampaigns as $camp): ?>
                <li>
                    <a href="index.php?action=campaign_view&id=<?= $camp['campaign_id']; ?>">
                        <strong><?= htmlspecialchars($camp['campaign_name']); ?></strong>
                    </a> 
                    (Kutsukoodi: <?= $camp['invite_code']; ?>)
                </li>
            <?php endforeach; ?>
        </ul>
    </section>


<?php require __DIR__ . '/partials/footer.php'; ?>