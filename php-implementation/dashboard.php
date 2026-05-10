<?php

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

    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    display: flex;
    height: 100vh;
    font-family: 'DM Sans', san-serif;
    background-color: #F6F4F1;
    overflow: hidden;
}

.sidebar {
    width: 260px;
    background: linear-gradient(to bottom, #801B32, #5A1424);
    color: #ffffff;
    display: flex;
    flex-direction: column;
    height: 100vh;
    flex-shrink: 0;
}

.logo {
    display: flex;
    align-items: center;
    padding: 30px 20px;
    gap: 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.logo img {
    width: 45px;
    height: 45px;
    object-fit: contain;
}

.brand {
    font-size: 24px;
    font-weight: 700;
}

.subtitle {
    font-size: 11px;
    opacity: 0.7;
}

nav {
    padding: 25px 15px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.nav-item {
    text-decoration: none;
    color: #ffffff;
    padding: 14px 18px;
    border-radius: 8px;
    font-size: 15px;
    transition: 0.3s;
}

.nav-item.active {
    background-color: #FFBF00;
    color: #801B32;
    font-weight: 700;
}

.nav-item:hover:not(.active) {
    background: rgba(255, 255, 255, 0.1);
}

.user-panel {
    margin-top: auto;
    padding: 25px 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.user-panel p {
    font-size: 14px;
    margin-bottom: 12px;
}

.logout-btn {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: none;
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff;
    font-weight: 600;
    cursor: pointer;
}

.main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    height: 100vh;
    overflow: hidden;
}

.top-nav {
    background: white;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #ddd;
}

.top-nav-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.nav-title {
    color: #801B32;
    font-weight: 700;
    font-size: 18px;
}

.top-nav-right {
    display: flex;
    align-items: center;
}

.user-badge {
    background: #801B32;
    color: white;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
    margin-right: 12px;
}

.user-name {
    font-weight: 500;
    color: #333;
}

.breadcrumb-bar {
    background: #D0D0D0;
    padding: 12px 30px;
    font-size: 12px;
    font-weight: 700;
    color: #444;
}

.dashboard-container {
    padding: 30px;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.welcome-text {
    color: #801B32;
    margin-bottom: 20px;
    font-size: 28px;
    font-weight: 700;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 25px;
    flex-shrink: 0;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.04);
    border-left: 5px solid transparent;
}

.blue-border {border-left-color: #3498db; }
.red-border {border-left-color: #e74c3c; }
.green-border {border-left-color: #2ecc71; }

.card-label { 
    font-size: 11px;
    color: #777;
    font-weight: 700;
    margin-bottom: 8px;
    letter-spacing: 0.5px;
}

.card-value {
    font-size: 32px;
    margin-bottom: 5px;
    font-weight: 600;
}

.card-sub {
    font-size: 11px;
    color: #999;
}

.color-red {
    color: #D0021B;
}

.color-blue {
    color: #4A90E2;
}

.color-green {
    color: #417505;
}

.main-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    flex: 1;
    min-height: 0;
}

.content-card {
    background: white;
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    display: flex;
    flex-direction: column;
    max-height: 100%;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
    flex-shrink: 0;
}

.list-container {
    flex: 1;
    overflow-y: auto;
    padding-right: 5px;
}

.list-container ::-webkit-scrollbar {
    width: 6px;
}

.list-container ::-webkit-scrollbar-thumb {
    background: #e0e0e0;
    border-radius: 10px;
}

.card-header h3 {
    color: #801B32;
    font-size: 24px;
    font-weight: 700;
}

.sub-text {
    color: #888;
    font-size: 13px;
    margin-top: 5px;
}

.view-report-btn {
    background: #801B32;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 700;
    cursor: pointer;
    font-size: 13px;
}

.placeholder-text {
    color: #bbb;
    font-style: italic;
    margin-top: 50px;
    text-align: center;
}
    </style>
</head>
<body>
    <aside class="sidebar">
            <div class="logo">
                <img src="image.png" alt="Logo">
                <div class="logo-text">
                    <span class="brand">PRISM</span>
                    <p class="subtitle">Admin Portal</p>
                </div>
            </div>

            <nav>
                <a href="#" class="nav-item active">Dashboard</a> 
                <a href="#" class="nav-item">Products</a>
                <a href="#" class="nav-item">Stock Movements</a>
                <a href="#" class="nav-item">Reports</a>
                <a href="#" class="nav-item">Profile</a>
            </nav>

            <div class="user-panel">
                <p>Admin User</p>
                <button class="logout-btn">Logout</button>
            </div>
        </aside>

        <div class="main-content">
            <header class="top-nav">
                <div class="top-nav-left">
                    <img src="image.png" alt="" style="width: 25px; height: 25px; object-fit: contain;">
                    <span class="nav-title">PRISM | Inventory Management</span>
                </div>
                <div class="top-nav-right">
                    <span class="user-badge">Admin</span>
                    <span class="user-name">Alex Reyes</span>
                </div>
            </header>

            <div class="breadcrumb-bar">
                PRISM / Inventory Management / Dashboard
            </div>

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
    </body>
</html>