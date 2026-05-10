<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISM Web App</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

    <div id="login-screen">
        <div class="logo-area">
            <img src="image.png" alt="PRISM Logo" class="main-logo">
            <h1 style="letter-spacing: 2px; margin: 0;">PRISM</h1>
        </div>

        <div class="login-card">
            <h2>Log in</h2>
            <form id="loginForm">
                <label>Email</label>
                <input type="text" id="userIn" placeholder="Enter your mail" required>

                <label>Password</label>
                <input type="password" id="passIn" placeholder="Enter password" required>

                <button type="submit" >Log in </button>
            </form>

            <div class="alert-box">
                <strong>Restricted access.</strong><br>
                All login attempts are logged and monitored.
            </div>
        </div>
     </div>

     <div id="dashboard-screen">
        <aside class="sidebar">
            <image scr="image.png" alt="PRISM Logo" style="width: 40px; margin-bottom: 20px;">

                <h3>PRISM</h3>
                <p style="opacity: 0.7; font-size: 14px;">Navigation Sidebar</p>
            </aside>
            <header class="header">Header inside body</header>
            <main class="main-body">
                <div class="main-body">
                    <h2>Content Area</h2>
                    <p>Authentication successful. Authorization enforced.</p>
                    <button onclick="location.reload()" style="width: auto; padding: 10px 20px;">Logout</button>
                </div>
            </main>
         </div>
    </body>
</html>