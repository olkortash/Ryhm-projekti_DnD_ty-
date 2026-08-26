<?php

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines !== false) {
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
            $key = trim($key);
            $value = trim($value);
            if ($key !== '') {
                putenv($key . '=' . $value);
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

function connectDB(){
    static $connection;
    if(!isset($connection)) {
        $connection = connect();
    }
    return $connection;
}

function connect() {
    $host = getenv('DB_HOST') ?: 'localhost';
    $port = getenv('DB_PORT') ?: 3306;
    $dbname = getenv('DB_NAME') ?: '';
    $user = getenv('DB_USERNAME') ?: '';
    $password = getenv('DB_PASSWORD') ?: '';

    if ($dbname === '' || $user === '') {
        throw new PDOException('Missing database configuration. Add DB_HOST, DB_PORT, DB_NAME, DB_USERNAME and DB_PASSWORD to the environment or .env file.');
    }

    $connectionString = "mysql:host=$host;dbname=$dbname;port=$port;charset=utf8";

    try {
        $pdo = new PDO($connectionString, $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        throw new PDOException('Virhe tietokantayhteydessä: ' . $e->getMessage());
    }
}