<?php

function connectDB(){
        static $connection;
        if(!isset($connection)) {
            $connection = connect();
        }      
        return $connection;
}

function connect() {
        $host = getenv('DB_HOST', true) ?: "ricsaa25.treok.io";
        $port = getenv('DB_PORT', true) ?: 3306; 
        $dbname = getenv('DB_NAME', true) ?: "ricsaa25_dnd_projekti"; 
        $user = getenv('DB_USERNAME', true) ?: "ricsaa25_dnd_projekti"; 
        $password = getenv('DB_PASSWORD', true) ?: "K&psOqm#o*Mh3+k#"; 

        $connectionString = "mysql:host=$host;dbname=$dbname;port=$port;charset=utf8";

        try {       
                $pdo = new PDO($connectionString, $user, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                echo "Connected successfully";
                return $pdo;
        } catch (PDOException $e){
                echo "Virhe tietokantayhteydessä: " . $e->getMessage();
                die();
        }
}