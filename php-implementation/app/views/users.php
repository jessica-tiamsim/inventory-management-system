<?php
// Top of users.php
session_start();

// 1. Check login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php?error=unauthorized");
    exit();
}

// 2. Enforce Role check (RBAC)
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php?error=access_denied");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PRISM | Inventory Management</title>
        <link rel="stylesheet" href="../../public/css/users.css">
        <link rel="stylesheet" href="../../public/css/sidebar_header.css">
        <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    </head>
    <body>
    <?php include __DIR__ . '/sidebar_header.php'; ?>
            <div class="content">
                
                <div class="page-header">
                    <h1 class="page-title">Users</h1>
                    <p class="page-subtitle">Manage your Employees</p>
                </div>

                <div class="action-bar">
                    <div class="search-and-button-group">
                            <div class="search-container">
                                <span class="search-icon">🔍</span>
                                <input type="text" placeholder="Search by Username" class="search-input">  
                            </div>
                            <button class="create-btn">
                                <span class="plus-icon">👤+</span> Create Account
                            </button>
                        </div>
                    </div>

                    <div class="table-container">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Jessica Tiamsim</td>
                                    <td>jc_admin@gmail.com</td>
                                    <td>Admin</td>
                                </tr>
                                <tr>
                                    <td>Wren Lacsamana</td>
                                    <td>wren_staff@gmail.com</td>
                                    <td>Staff</td>
                                </tr>
                                <tr>
                                    <td>Renzo Areta</td>
                                    <td>renzo_staff@gmail.com</td>
                                    <td>Staff</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>