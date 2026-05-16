<?php

require_once __DIR__ . '../../../vendor/autoload.php';

// This is the "magic" line that loads your .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../'); 
$dotenv->load();

// NOW you can require your database connection
require_once __DIR__ . '/../models/db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width-device-width, initial-scale=1.0">
        <title>PRISM</title>
        <link rel="stylesheet" href="../../public/css/index.css">
    </head>
    <body>
        <div class="login_screen">

            <div class = "logo_div">
                
                <img src="/inventory-management-system/assets/logo.png" class = "prism_img">
                    <h1 class = "logo_text">PRISM</h1>
            </div>
            
            <div class="login_card">
                <h2>Log in</h2>
                <form method="POST" action="../controller/login_controller.php">
                    <label for="username_email">Username or Email</label>
                    <input type="text" name="username_email" placeholder="Username or Email" required>
                    <label for="passsword">Password</label>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" class="btn-gold">Log in</button>
                </form>
                <div class = "login_description">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert-box">
                            <?php 
                                $error = $_GET['error'];
                                if ($error === 'invalid_login') {
                                    echo "<p class='error-msg'><strong>Error:</strong> The username/email or password is incorrect.</p>";
                                } elseif ($error === 'deactivated') {
                                    echo "<p class='error-msg'><strong>Account Suspended:</strong> This account has been deactivated. Please contact your system administrator.</p>";
                                } elseif ($error === 'unauthorized') {
                                    echo "<p class='error-msg'>Please log in first to access the app.</p>";
                                } else {
                                    echo "<p class='error-msg'>An unexpected server error occurred.</p>";
                                }
                            ?>
                        </div>
                        <?php else: ?>
                        <p>Restricted access.</p>
                        <p>All login attempts are logged and monitored.</p>
                    <?php endif; ?>
                </div>
            </div>  
        </div>
    </body>
</html>



