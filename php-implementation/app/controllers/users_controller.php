<?php
// app/controllers/ProfileController.php

class ProfileController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // 1. DISPLAY THE USERS PAGE (Your existing code)
    public function index() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: " . BASE_URL . "/login");
            exit();
        }

        $search = $_GET['search'] ?? '';
        $where_clause = '';
        $params = [];

        if (!empty($search)) {
            $where_clause = "WHERE username LIKE :search OR email LIKE :search";
            $params['search'] = '%' . $search . '%';
        }

        try {
            $sql = "SELECT id, username, email, role FROM users $where_clause ORDER BY role ASC, username ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("User Fetch Error: " . $e->getMessage());
            $users = [];
        }

        require_once __DIR__ . '/../views/users.php';
    }

    // 2. PROCESS NEW USER CREATION (New Code!)
    public function add() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: " . BASE_URL . "/login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $role = $_POST['role'];

            // Security: Hash the password before saving to the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            try {
                $sql = "INSERT INTO users (username, email, password, role) VALUES (:user, :email, :pass, :role)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    'user' => $username,
                    'email' => $email,
                    'pass' => $hashed_password,
                    'role' => $role
                ]);
                
                // Redirect back to profile page on success
                header("Location: " . BASE_URL . "/users?success=user_added");
                exit();
                
            } catch (PDOException $e) {
                error_log("User Creation Error: " . $e->getMessage());
                // Redirect back with an error message
                header("Location: " . BASE_URL . "/users?error=creation_failed");
                exit();
            }
        }
    }
}