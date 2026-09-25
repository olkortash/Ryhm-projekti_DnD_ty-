<?php
require_once __DIR__ . '/../connection.php';

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function register($username, $email, $password) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password_hash' => $passwordHash
        ]);
    }

    public function login($username, $password) {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return false;
    }

    public function searchByUsername(string $query): array {
        // LIKE-erikoismerkit käsitellään tavallisina hakumerkkeinä. Haku ei palauta tilin yksityisiä tietoja.
        $pattern = '%' . strtr($query, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
        $stmt = $this->pdo->prepare(
            "SELECT username FROM users
             WHERE username LIKE :query ESCAPE '!'
             ORDER BY username, user_id LIMIT 21"
        );
        $stmt->execute([':query' => $pattern]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($user_id) {
        $sql = "SELECT user_id, username, email, created_at FROM users WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
