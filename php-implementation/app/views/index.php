<?php
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width-device-width, initial-scale=1.0">
        <title>PRISM Web App</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <div class="login-screen">
            <div class = "login_div">
                <img src="logo.png" alt="PRISM Logo" class = "prism_img">
                    <h1 style="letter-spacing: 2px; margin: 0;">PRISM</h1>
            </div>
            <div class="login-card">
                <h2 style="color: var(--maroon); margin-bottom: 20px;">Log in</h2>
                <form method="POST" action="login.php">
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" class="btn-gold">Log in</button>
                </form>
            </div>
        </div>
    </body>
</html>



