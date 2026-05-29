<?php
// app/controllers/AuthController.php

class AuthController {
    private $pdo;

    // The front controller passes the database connection in here
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // --- Handles GET requests to /login ---
    public function showLoginForm() {
        $errorMessage = '';

        if (isset($_GET['error'])) {
            $error = $_GET['error'];
            if ($error === 'invalid_login') {
                $errorMessage = "Error: The username/email or password is incorrect.";
            } elseif ($error === 'deactivated') {
                $errorMessage = "Account Suspended: This account has been deactivated. Please contact your system administrator.";
            } elseif ($error === 'empty_fields') {
                $errorMessage = "Please enter both username/email and password.";
            } elseif ($error === 'server_error') {
                $errorMessage = "An unexpected server error occurred.";
            } else {
                $errorMessage = "Please log in first to access the app.";
            }
        }

        require_once __DIR__ . '/../views/login.php';
    }

    // --- Handles POST requests from the login form ---
    public function loginPost() {
        // Trim removes accidental spaces
        $username_email = trim($_POST['username_email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username_email) || empty($password)) {
            // Redirect to the clean URL route, not the physical file
            header("Location: /login?error=empty_fields");
            exit();
        }

        try {
            // Notice we use $this->pdo here
            // 1. Give each check its own unique named placeholder (:username and :email)
            $stmt = $this->pdo->prepare("SELECT id, username, password_hash, role, is_active FROM users WHERE (username = :username OR email = :email) LIMIT 1");

            // 2. Pass the user's input variable to BOTH placeholders explicitly
            $stmt->execute([
                'username' => $username_email,
                'email'    => $username_email
            ]);

            // 3. Fetch the user record normally
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify user exists, check password hash
            if ($user && password_verify($password, $user['password_hash'])) {
                
                // Check activation status
                if ($user['is_active'] == 1) {
                    
                    // SUCCESS: Secure the session
                    session_regenerate_id(true);

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['logged_in'] = true;

                    // Redirect to the dashboard route
                    header("Location: dashboard");
                    exit();
                } else {
                    // Account exists but is deactivated
                    header("Location: login?error=deactivated");
                    exit();
                }
            } else {
                // Either user doesn't exist or password didn't match
                header("Location: login?error=invalid_login");
                exit();
            }

        } catch (PDOException $e) {
            // Log the technical error internally; show a generic error to the user
            error_log("Login Error: " . $e->getMessage());
            header("Location: login?error=server_error");
            exit();
        }
    }

    // --- Handles GET requests to /logout ---
    public function logout() {
        // 1. Destroy the session
        session_unset();
        session_destroy();

        // 2. Load the beautiful logout success view instead of redirecting
        require_once __DIR__ . '/../views/logout.php';
        exit();
    }
}