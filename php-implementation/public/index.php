<?php
// public/index.php

// 1. Bootstrap & Autoloading
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// 2. Secure Session Initialization
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => '', 
    'secure' => isset($_SERVER['HTTPS']), 
    'httponly' => true, 
    'samesite' => 'Lax'
]);

session_start();

// 3. Database Initialization
// Requiring your database connection file location
require_once __DIR__ . '/../app/models/db_connection.php';

// If db_connection.php defines a Class named Database, this functions perfectly:
$databaseInstance = new Database();
$pdo = $databaseInstance->getConnection();

// ========================================================
// 4. Advanced Routing System (Normalizes Trailing Slashes)
// ========================================================
$rawPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Automatically captures the base folder paths (XAMPP friendly)
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
define('BASE_URL', $scriptDir);
$cleanPath = str_replace($scriptDir, '', $rawPath);

$requestUri = '/' . trim($cleanPath, '/');
define('CURRENT_ROUTE', $requestUri);
$method = $_SERVER['REQUEST_METHOD'];
// A simple switch statement acts as our router matching clean URLs
switch ($requestUri) {
    case '/':
        header("Location: " . BASE_URL . "/login");
        exit();

    case '/dashboard':
        require_once __DIR__ . '/../app/controllers/dashboard_controller.php';
        $controller = new DashboardController($pdo);
        $controller->index();
        break;

    case '/login':
        require_once __DIR__ . '/../app/controllers/login_controller.php';
        $controller = new AuthController($pdo);
        if ($method === 'POST') {
            $controller->loginPost();
        } else {
            $controller->showLoginForm();
        }
        break;

    case '/logout':
        require_once __DIR__ . '/../app/controllers/login_controller.php';
        $controller = new AuthController($pdo);
        $controller->logout();
        break;
    
    case '/products':
        require_once __DIR__ . '/../app/controllers/products_controller.php';
        $products = new ProductsController($pdo);
        $products->index(); // Loads the table view
        break;

    case '/products/add':
        require_once __DIR__ . '/../app/controllers/products_controller.php';
        $products = new ProductsController($pdo);
        $products->add(); // Processes the Add Product modal form
        break;

    case '/products/edit':
        require_once __DIR__ . '/../app/controllers/products_controller.php';
        $products = new ProductsController($pdo);
        $products->edit();
        break;

    case '/products/delete':
        require_once __DIR__ . '/../app/controllers/products_controller.php';
        $products = new ProductsController($pdo);
        $products->delete();
        break;

    // ----------------- STOCK MOVEMENTS -----------------
    // Pointing exactly to your stock_movements_controller.php file
    case '/stock-movement':
        require_once __DIR__ . '/../app/controllers/stock_movements_controller.php';
        // Make sure your class name matches what is inside that file (e.g., StockMovementsController)
        $movements = new StockMovementsController($pdo);
        $movements->index();
        break;

    case '/stock-movement/add':
        require_once __DIR__ . '/../app/controllers/stock_movements_controller.php';
        $movements = new StockMovementsController($pdo);
        $movements->add();
        break;
    
    // =========================================================
    // ----------------- REPORTS MODULE TABS -------------------
    // =========================================================
    
    case '/reports':
    case '/reports/low_stock':
        require_once __DIR__ . '/../app/controllers/reports_controller.php';
        $reports = new ReportsController($pdo);
        $reports->low_stock(); 
        break;

    case '/reports/valuation':
        require_once __DIR__ . '/../app/controllers/reports_controller.php';
        $reports = new ReportsController($pdo);
        $reports->valuation(); 
        break;

    case '/reports/movement_ledger':
        require_once __DIR__ . '/../app/controllers/reports_controller.php';
        $reports = new ReportsController($pdo);
        $reports->movementLedger(); 
        break;

    case '/reports/top_movers':
        require_once __DIR__ . '/../app/controllers/reports_controller.php';
        $reports = new ReportsController($pdo);
        $reports->topMovers(); 
        break;

        // ----------------- PROFILE / USERS -----------------
    case '/profile':
        require_once __DIR__ . '/../app/controllers/profile_controller.php';
        $profile = new ProfileController($pdo);
        $profile->index();
        break;

    case '/profile/add':
        require_once __DIR__ . '/../app/controllers/profile_controller.php';
        $profile = new ProfileController($pdo);
        $profile->add();
        break;
    // ----------------- FALLBACK 404 ERROR -----------------
    default:
        http_response_code(404);
        echo "<div style='text-align:center; padding:100px; font-family:sans-serif;'>";
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>The route <strong>" . htmlspecialchars(CURRENT_ROUTE) . "</strong> does not exist in the PRISM system.</p>";
        echo "<a href='" . BASE_URL . "/dashboard' style='color:#801B32;'>Return to Dashboard</a>";
        echo "</div>";
        break;

}