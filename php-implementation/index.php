<?php

session_start();

$host = 'localhost';
$db   = 'inventory_db';
$user = 'root';
$pass = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("System Error: Connection failed.");
}

$page = $_GET['page'] ?? 'dashboard';

if (!isset($_SESSION['user_id']) && $page !== 'login') {
    header("Location: index.php?page=login");
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Inventory | <?= ucfirst($page) ?></title>
</head>
<body>
    <main>
        <?php 
        switch ($page) {
            case 'login':
                echo "<h1>Login Screen</h1>";
                break;
            case 'products':
                echo "<h1>Product Inventory</h1>";
                break;
            default:
                echo "<h1>Dashboard</h1>";
                break;
        }
        ?>
    </main>
</body>
</html>