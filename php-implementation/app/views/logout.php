<?php
// logout.php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy cookie references if active
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Vaporize the server session object completely
session_destroy();

// Redirect clean back to landing view
header("Location: index.php?msg=logged_out");
exit();