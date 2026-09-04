<?php
$target_dir = "uploads/";
$uploadOk = 0;
//^^^^EI TOIMI VIELÄ^^^^
$pageTitle = "Hahmon Tiedot - Roolipelisovellus";
require __DIR__ . '/partials/head.php';
?>
<!--EI TEE VIELÄ MITÄÄN-->
<h1><?= htmlspecialchars($character['character_name']); ?></h1>
<p>Taso: <?= $character['level']; ?> | Rotu: <?= $character['race_name']; ?> | Luokka: <?= $character['class_name']; ?> | Ammatti: <?= $character['job_name']; ?></p>
<p>Kampanja: <?= $character['campaign_name'] ? htmlspecialchars($character['campaign_name']) : "Ei kampanjassa"; ?></p>

<?php if(!$character['character_img_id']): ?>

    <?php
    if(isset($_POST["submit"])) {
        $imageFileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));

        if ($imageFileType == "jpg" || $imageFileType == "png") {
            $fileName = $character['character_id'] . '.' . $imageFileType;
            $target_file = $target_dir . $fileName;
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);

            if($check !== false) {
                if (!file_exists($target_file)) {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                        echo "The file " . htmlspecialchars($fileName) . " has been uploaded.";

                        // Tallenna sama nimi tietokantaan
                        // Esimerkki:
                        // UPDATE characters SET character_img_id = ? WHERE character_id = ?
                    } else {
                        echo "There was an error uploading the image.";
                    }
                } else {
                    echo "File already exists.";
                }
            } else {
                echo "File is not an image.";
            }
        } else {
            echo "not target filetype";
        }
    }
    ?>

    <form method="post" enctype="multipart/form-data">
        lisää hahmolle kuva tästä:
        <input type="file" name="fileToUpload" id="fileToUpload"><br>
        <input type="submit" value="Upload Image" name="submit">
    </form>

<?php else: ?>
    <img src="uploads/<?=$character["character_img_id"]; ?>" alt="kuva">
<?php endif; ?>
<!--^^^^^^^^ EI TEE VIELÄ MITÄÄN ^^^^^^^^-->
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