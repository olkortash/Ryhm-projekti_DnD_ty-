<?php
require_once __DIR__ . '/../database/models/users.php';

function cleanUpInput($value) {
    return trim(strip_tags((string)$value));
}

function addUser($username, $email, $password) {
    $pdo = connectDB();
    $userModel = new User($pdo);
    return $userModel->register($username, $email, $password);
}

function login($username, $password) {
    $pdo = connectDB();
    $userModel = new User($pdo);
    return $userModel->login($username, $password);
}

function registerController(){
    if(isset($_POST['username'], $_POST['email'], $_POST['password'])){
        $username = cleanUpInput($_POST['username']);
        $email = cleanUpInput($_POST['email']);
        $password = cleanUpInput($_POST['password']);

        try {
            addUser($username, $email, $password);
            header("Location: index.php?action=login");
            exit;
        } catch (PDOException $e){
            echo "Virhe tietokantaan tallennettaessa: " . $e->getMessage();
        }
    } else {
        require __DIR__ . '/../views/login.php';
    }
}

function loginController(){
    if(isset($_POST['username'], $_POST['password'])){
        $username = cleanUpInput($_POST['username']);
        $password = cleanUpInput($_POST['password']);

        $result = login($username, $password);
        if($result){
            $_SESSION['username'] = $result['username'];
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['session_id'] = session_id();
            header("Location: index.php?action=dashboard");
            exit;
        } else {
            require __DIR__ . '/../views/login.php';
        }
    } else {
        require __DIR__ . '/../views/login.php';
    }
}

function logoutController(){
    session_unset();
    session_destroy();
    setcookie(session_name(), '', 0, '/');
    session_regenerate_id(true);
    header("Location: index.php?action=login");
    exit;
}