<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

// These would ideally come from a Database Query later
$active_products = 7;
$total_units = 193;
$inventory_value = 1403;
$low_stock_count = 4;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISM | Inventory Management</title>
    <link rel="stylesheet" href="../../public/css/dashboard.css">
    <link rel="stylesheet" href="../../public/css/sidebar_header.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
</head>
<body>
    <?php include __DIR__ . '/sidebar_header.php'; ?>

        <div class="content">

            <div class="dashboard-container">
                <h1 class="welcome-text">Welcome back, Alex!</h1>

                <div class="stats-grid">

                    <div class="stat-card blue-border">
                        <p class="card-label">ACTIVE PRODUCTS</p>
                        <h2 class="card-value color-blue">7</h2>
                        <p class="card-sub">2 Inactive</p>
                    </div>
                    <div class="stat-card red-border">
                        <p class="card-label">UNITS IN STOCK</p>
                        <h2 class="card-value color-red">193</h2>
                        <p class="card-sub">across all SKUs</p>
                    </div>
                    <div class="stat-card blue-border">
                        <p class="card-label">INVENTORY VALUE</p>
                        <h2 class="card-value color-blue">₱1, 403</h2>
                        <p class="card-sub">at unit cost</p>
                    </div>
                    <div class="stat-card green-border">
                        <p class="card-label">LOW STOCK ALERTS</p>
                        <h2 class="card-value color-green">4</h2>
                        <p class="card-sub">at or below threshold</p>
                    </div>
                </div>

                <div class="main-grid">
                    <section class="content-card">
                        <div class="card-header">
                            <div>
                                <h3>Low Stock</h3>
                                <p class="sub-text">Products at or below reorder threshold</p>
                            </div>
                            <button class="view-report-btn">View Report</button>
                        </div>
                        <div class="list-container">
                            <p class="placeholder-text">Fetching data...</p>
                        </div>
                    </section>

                    <section class="content-card">
                        <div class="card-header">
                            <div>
                                <h3>Recent Activity</h3>
                                <p class="sub-text">Latest stock movements</p>
                            </div>
                        </div>
                        <div class="list-container">
                            <p class="placeholder-text">No recent activity found.</p>
                        </div>
                    </section>
                </div>
            </div>
        </div>
</div>
    </body>
</html>