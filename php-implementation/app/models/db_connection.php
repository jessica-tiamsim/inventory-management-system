<?php
// app/models/Database.php

class Database {
    private $pdo;

    public function __construct() {
        $host = $_ENV['DB_HOST'];
        $db   = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // CRITICAL ADDITION: Forces native prepared statements for security
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            
        } catch (PDOException $e) {
            // 1. Log the actual error to your server's hidden error log
            error_log("Connection failed: " . $e->getMessage());
            
            // 2. Safely kill the script with a generic message for the user
            die("A database error occurred. Please try again later.");
        }
    }

    // Your controllers will call this to get the safe connection
    public function getConnection() {
        return $this->pdo;
    }
}