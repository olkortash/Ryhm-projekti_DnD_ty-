<?php 
$pageTitle = "Kampanjan Hallinta - Roolipelisovellus";
require __DIR__ . '/partials/head.php'; 
?>


    <h1>Manage Campaign: <?= htmlspecialchars($campaign['campaign_name']); ?></h1>
    <p><strong>Invite Code for Players:</strong> <code><?= $campaign['invite_code']; ?></code></p>

    <form action="index.php?action=campaign_update&redirect=dashboard" method="POST">
        <input type="hidden" name="campaign_id" value="<?= $campaign['campaign_id']; ?>">
        <label>Campaign Name:<br>
            <input type="text" name="campaign_name" value="<?= htmlspecialchars($campaign['campaign_name']); ?>" required>
        </label><br>
        <label>Description:<br>
            <textarea name="description"><?= htmlspecialchars($campaign['description']); ?></textarea>
        </label><br>
        <button type="submit" formaction="index.php?action=campaign_update&redirect=dashboard">Update Campaign</button>
        <button type="submit" name="delete_campaign" value="1" formaction="index.php?action=campaign_delete&redirect=dashboard">Delete Campaign</button>
    </form>

    <hr>

    <h2>Kampanjan Hahmot</h2>
    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>Pelaaja</th>
                <th>Hahmo</th>
                <th>Rotu / Luokka</th>
                <th>HP (Nykyinen / Max)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($players as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['player_name']); ?></td>
                    <td><?= htmlspecialchars($p['character_name']); ?></td>
                    <td><?= $p['race_name']; ?> / <?= $p['class_name']; ?></td>
                    <td><?= $p['hp_current']; ?> / <?= $p['hp_max']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a href="index.php?action=dashboard">Palaa päänäkymään</a>



<?php require __DIR__ . '/partials/footer.php'; ?>