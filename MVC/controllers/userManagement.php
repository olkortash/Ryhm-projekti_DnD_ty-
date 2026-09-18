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
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 3600,
            $params['path'],
            $params['domain'] ?? '',
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
    session_regenerate_id(true);
    header("Location: index.php?action=login");
    exit;
}