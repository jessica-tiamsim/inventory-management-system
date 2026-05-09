<?php

$displayLogin = "flex";
$displayDash = "none";
$bodyBg = "linear-gradient(180deg, #801B32 0%, #5A1424 100%)";
$errorAlert = false;

if (isset($_GET['logout'])) {
    $displayLogin = "flex";
    $displayDash = "none";
    $bodyBg = "linear-gradient(180deg, #801B32 0%, #5A1424 100%)";
} 
else if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"]== "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email === "jc_admin@prism.com" && $password === "password1217") {
        $displayLogin = "none";
        $displayDash = "grid";
        $bodyBg = "#ffffff";
    } else {
        $errorAlert = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width-device-width, initial-scale=1.0">
        <title>PRISM Web App</title>
        <style>
            :root {
                --gold: #FFBF00;
                --maroon: #801B32;
                --white: #FFFFFF;
            }

            body {
                margin: 0;
                font-family: sans-serif;
                height: 100vh;
                background: <?php echo $bodyBg; ?>;
                transition: background 0.3s ease;
            }

            #login-screen {
                display: <?php echo $displayLogin; ?>;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                height: 100%;
            }

            #dashboard-screen {
                display: <?php echo $displayDash; ?>;
                grid-template-columns: 250px 1fr;
                height: 100vh;
            }

            .login-card {
                background: white;
                padding: 40px;
                border-radius: 16px;
                width: 320px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                text-align: center;
            }

            input {
                width: 100%;
                padding: 12px;
                margin: 10px 0;
                border: 1px solid #ddd;
                border-radius: 8px;
                box-sizing: border-box;
            }

            .btn-gold {
                width: 100%;
                background: var(--gold);
                border: none;
                padding: 14px;
                border-radius: 8px;
                font-weight: bold;
                cursor: pointer;
            }

            .sidebar {
                background: var(--maroon);
                color: white;
                padding: 20px;
            }

            .logout-btn {
                background: #ff4444;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div id="login-screen">
            <div style="text-align: center; color: white; margin-bottom: 20px;">
                <img src="logo.png" alt="PRISM Logo" style="width: 80px; display: block; margin: 0 auto 10px;">
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

            <div id="dashboard-screen">
                <aside class="sidebar">
                    <h2>PRISM</h2>
                    <p>Admin Panel</p>
                    <button class="logout-btn" onclick="window.location.href='login.php?logout=true'">
                        Logout
                    </button>
                </main>
            </div>

            <?php if ($errorAlert): ?>
                <script>
                    alert("The login credentials doesn't match an account in the system.");
                    window.history.replaceState({}, document.title, "login.php");
                </script>
                <?php endif; ?>
            </body>
            </html>



