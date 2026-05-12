<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Load dependencies (This connects the DB via your index.php logic)
require_once __DIR__ . '/../../app/views/index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 2. Capture and sanitize input
    // Trim removes accidental spaces; htmlspecialchars handles security for display later
    $username_email = trim($_POST['username_email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username_email) || empty($password)) {
        header("Location: ../views/index.php?error=empty_fields");
        exit();
    }
try {
        // 3. Match the query to your ACTUAL database columns (user_is_active)
        $stmt = $pdo->prepare("SELECT id, username, password_hash, role, user_is_active FROM users WHERE (username = :input OR email = :input) LIMIT 1");
        $stmt->execute(['input' => $username_email]);
        $user = $stmt->fetch();

        // 4. Verify user exists, check password hash, and check activation status
        if ($user && password_verify($password, $user['password_hash'])) {
            
            if ($user['user_is_active'] == 1) {
                // SUCCESS: Secure the session
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['logged_in'] = true;

                // 5. Redirect to dashboard
                header("Location: ../views/dashboard.php");
                exit();
            } else {
                // Account exists but is deactivated
                header("Location: ../views/index.php?error=inactive");
                exit();
            }
        } else {
            // Either user doesn't exist or password didn't match
            header("Location: ../views/index.php?error=invalid_login");
            exit();
        }

    } catch (PDOException $e) {
        // Log the technical error internally; show a generic error to the user
        error_log("Login Error: " . $e->getMessage());
        header("Location: ../views/index.php?error=server_error");
        exit();
    }
} else {
    header("Location: ../views/index.php");
    exit();
}