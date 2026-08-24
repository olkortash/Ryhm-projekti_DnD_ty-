<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/create_character.php');
    exit;
}

$playerId = current_user_id();
$characterName = trim($_POST['character_name'] ?? '');
$classId = filter_input(INPUT_POST, 'character_class_id', FILTER_VALIDATE_INT);
$raceId  = filter_input(INPUT_POST, 'character_race_id', FILTER_VALIDATE_INT);
$jobId   = filter_input(INPUT_POST, 'character_job_id', FILTER_VALIDATE_INT);
$hpMax   = filter_input(INPUT_POST, 'hp_max', FILTER_VALIDATE_INT) ?? 10;

if (!$characterName || !$classId || !$raceId || !$jobId) {
    die('Virheelliset tai puuttuvat tiedot.');
}

$sql = "INSERT INTO characters 
        (player_id, campaign_id, character_name, character_class_id, character_race_id, character_job_id, level, hp_current, hp_max) 
        VALUES 
        (:player_id, NULL, :character_name, :class_id, :race_id, :job_id, 1, :hp_current, :hp_max)";

$stmt = $pdo->prepare($sql);

$success = $stmt->execute([
    ':player_id'      => $playerId,
    ':character_name' => $characterName,
    ':class_id'       => $classId,
    ':race_id'        => $raceId,
    ':job_id'         => $jobId,
    ':hp_current'     => $hpMax,
    ':hp_max'         => $hpMax
]);

if ($success) {
    header('Location: ../index.php?status=character_created');
    exit;
} else {
    echo "Virhe hahmon tallennuksessa.";
}