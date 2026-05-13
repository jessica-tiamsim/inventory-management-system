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
        <title>PRISM Web App</title>
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
                    <input type="text" name="username_email" placeholder="Username or Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" class="btn-gold">Log in</button>
                </form>
                <div class = "login_description">
                
                </div>
            </div>  
        </div>
    </body>
</html>



