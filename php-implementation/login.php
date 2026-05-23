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
else if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISM Web App</title>
    <style>
        :root {
            --gold: #FFBF00;
            --maroon: #801B32;
            --white: #FFFFFF;
            --light-bg: #F8F9FA;
            --error-red: #FEEFB3; /* Adjusted based on the image's light red tint */
        }

        body {
            margin: 0;
            font-family: sans-serif;
            height: 100vh;
            background: <?php echo $bodyBg; ?>;
            transition: background 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #login-screen {
            display: <?php echo $displayLogin; ?>;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .brand-section {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }

        .brand-section img {
            width: 70px;
            margin-bottom: 10px;
        }

        .brand-section h1 {
            letter-spacing: 4px;
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }

        .login-card {
            background: white;
            padding: 40px 30px;
            border-radius: 24px; /* More rounded per image */
            width: 380px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            text-align: left; /* Align labels left */
        }

        .login-card h2 {
            color: var(--maroon);
            margin-top: 0;
            margin-bottom: 30px;
            text-align: center;
            font-size: 32px;
        }

        label {
            display: block;
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 14px;
            margin-bottom: 20px;
            border: 1px solid #EAEAEA;
            border-radius: 12px;
            box-sizing: border-box;
            background-color: #FBFBFB;
            font-size: 14px;
        }

        input::placeholder {
            color: #A0A0A0;
        }

        .btn-gold {
            width: 100%;
            background: #FFC107; /* Bright gold from image */
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 16px;
            color: #5A1424;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(255, 193, 7, 0.3);
            margin-top: 10px;
        }

        /* The specific error box design from your image */
        .error-container {
            margin-top: 25px;
            padding: 15px;
            background-color: #FFF2F2;
            border: 1px solid #FFDADA;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .error-icon {
            color: #D32F2F;
            border: 2px solid #D32F2F;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            flex-shrink: 0;
        }

        .error-text {
            color: #801B32;
            font-size: 13px;
            line-height: 1.4;
        }

        .error-text strong {
            display: block;
            font-size: 14px;
        }

        #dashboard-screen {
            display: <?php echo $displayDash; ?>;
            grid-template-columns: 250px 1fr;
            width: 100%;
            height: 100vh;
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
        <div class="brand-section">
            <img src="logo.png" alt="PRISM Logo">
            <h1>PRISM</h1>
        </div>

        <div class="login-card">
            <h2>Log in</h2>
            <form method="POST" action="login.php">
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter your mail" required>
                
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter Password" required>
                
                <button type="submit" class="btn-gold">Log in</button>
            </form>

            <?php if ($errorAlert): ?>
            <div class="error-container">
                <div class="error-icon">!</div>
                <div class="error-text">
                    <strong>Account Deactivated</strong>
                    Contact Admin for more Information.
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="dashboard-screen">
        <aside class="sidebar">
            <h2>PRISM</h2>
            <p>Admin Panel</p>
            <button class="logout-btn" onclick="window.location.href='login.php?logout=true'">
                Logout
            </button>
        </aside>
        <main style="padding: 20px;">
            <h1>Welcome to Dashboard</h1>
        </main>
    </div>

</body>
</html>