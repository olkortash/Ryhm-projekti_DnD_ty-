<?php
require_once "database/connection.php";

function addUser($username, $email, $password){
    $pdo = connectDB();
    $hashedpassword = hashPassword($password);
    $data = [$username, $email, $hashedpassword];
    $sql = "INSERT INTO users (username, email, password) VALUES(?,?,?)";
    $stm=$pdo->prepare($sql);
    return $stm->execute($data);
}

function login($username, $password){
    $pdo = connectDB();
    $sql = "SELECT * FROM users WHERE username=?";
    $stm= $pdo->prepare($sql);
    $stm->execute([$username]);
    $user = $stm->fetch(PDO::FETCH_ASSOC);
    $hashedpassword = $user["password"];

    if($hashedpassword && password_verify($password, $hashedpassword))
        return $user;
    else 
        return false;
}