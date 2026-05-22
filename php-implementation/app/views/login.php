
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width-device-width, initial-scale=1.0">
        <title>PRISM</title>
        <link rel="stylesheet" href="css/global.css">
        <link rel="stylesheet" href="css/login.css">
    </head>
    <body>
        <div class="login_screen">

            <div class = "logo_div">
                
                <img src="assets/logo.png" class = "prism_img">
                    <h1 class = "logo_text">PRISM</h1>
            </div>
            
            <div class="login_card">
                <h2>Log in</h2>
                <form method="POST" action="">
                    <label for="username_email">Username or Email</label>
                    <input type="text" name="username_email" id="username_email" placeholder="Username or Email" required>
                    
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    
                    <button type="submit" class="btn-gold">Log in</button>
                </form>
                <div class = "login_description">
                    <?php if (!empty($errorMessage)): ?>
                        <div class="alert-box">
                            <p class='error-msg'><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
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



