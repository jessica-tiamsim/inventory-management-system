<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISM | Logout Successful</title>
    <link href='https://fonts.googleapis.com/css?family=Inter:wght@400;500;600;700&display=swap' rel='stylesheet'>
    <style>
        :root {
            --maroon: #801B32; 
            --maroon-dark: #5A1424;
            --text-dark: #1A1714;
            --text-muted: #555555;
        }
        
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background-color: var(--maroon);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }

        .brand-header {
            text-align: center;
            color: white;
            margin-bottom: 25px;
        }

        .brand-header svg {
            width: 55px;
            height: 55px;
            margin-bottom: 8px;
        }

        .brand-header h1 {
            margin: 0;
            font-size: 22px;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .logout-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            box-sizing: border-box;
        }

        .success-icon {
            width: 60px;
            height: 60px;
            border: 2px solid var(--maroon);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
        }

        .success-icon svg {
            width: 32px;
            height: 32px;
            color: var(--maroon);
        }

        .logout-card h2 {
            color: var(--maroon);
            margin: 0 0 15px 0;
            font-size: 22px;
            font-weight: 700;
        }

        .logout-card p {
            color: var(--text-dark);
            font-size: 13px;
            line-height: 1.6;
            margin: 0 0 30px 0;
        }

        .login-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            background-color: var(--maroon-dark); 
            color: white;
            text-decoration: none;
            padding: 14px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: transform 0.2s, background-color 0.2s;
            box-sizing: border-box;
        }

        .login-btn:hover {
            background-color: #4A101D;
            transform: translateY(-2px);
        }

        .login-btn svg {
            margin-left: 8px;
        }
    </style>
</head>
<body>

    <div class="brand-header">
        <img>
        <h1>PRISM</h1>
    </div>

    <div class="logout-card">
        <div class="success-icon">
            <img>
        </div>
        
        <h2>Logout Successful</h2>
        
        <p>
            You have been successfully logged out.<br>
            To access your account again, please<br>
            click Log In Again.
        </p>
        
        <a href="<?= BASE_URL ?>/login" class="login-btn">
            Log In Again
            <img>
        </a>
    </div>

</body>
</html>