<?php
require_once "database/connection.php";

function addCharacter($character_name, $character_class_id, $character_race_id, $character_job_id, $level, $hp_max, $player_id){
    $pdo =connectDB();
    $data = [$character_name, $character_class_id, $character_race_id, $character_job_id, $level, $hp_max, $player_id];
    $sql = "INSERT INTO characters (character_name, character_class_id, character_race_id, character_job_id level, hp_max) VALUES (?, ?, ?)";
    $sql = "INSERT INTO classes (class_id, class_name) VALUES (?, ?)";
    $sql = "INSERT INTO races (race_id, race_name) VALUES (?, ?)";
    $sql = "INSERT INTO jobs (job_id, job_name) VALUES (?, ?)";
    $stm=$pdo->prepare($sql);
    return $stm->execute($data);
}

function updateCharacter($name, $character_class, $character_id){
    $pdo =connectDB();
    $data = [$name, $character_class, $hp_max, $character_id];
    $sql = "UPDATE characters SET name = ?, character_class = ? WHERE character_id = ?";
    $stm=$pdo->prepare($sql);
    return $stm->execute($data);
}

function deleteCharacter($character_id){
    $pdo = connectDB();
    $sql = "DELETE FROM characters WHERE character_id=?";
    $stm=$pdo->prepare($sql);
    return $stm->execute([$id]);
}