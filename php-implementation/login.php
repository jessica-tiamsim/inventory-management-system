<?php

$displayLogin = "flex";
$displayDash = "none";
$bodyBg = "linear-gradient(180deg, #801B32 0%, #5A1424 100%)";
$errorAlert = false;

if (isset($_GET['logout'])) {
    $displayLogin = "flex";
    $displayDash = "none";
    $bodyBg = "linear-gradient(180deg, #801B32 0%, #5A1424 100%)";
} else if ($_SERVER["REQUEST METHOS"] == "POST") {
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

            


