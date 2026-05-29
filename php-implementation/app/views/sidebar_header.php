
<aside>
    <div class="header_div">
        <div class="logo_div">
            <img src="<?= BASE_URL ?>/assets/logo.png" class="logo_image" alt="PRISM Logo">
            <h1>PRISM</h1>
        </div>
        <div class="logo_user">
            <p><?= (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'Admin Portal' : 'Staff Portal' ?></p>
        </div>
    </div>
    
    <nav>
        <a href="<?= BASE_URL ?>/dashboard" class="<?= (CURRENT_ROUTE == '/dashboard') ? 'active' : '' ?>">
            <img src="<?= BASE_URL ?>/assets/dash_icon.png" alt="Dashboard">
            <p>Dashboard</p>
        </a>
        
        <a href="<?= BASE_URL ?>/products" class="<?= (CURRENT_ROUTE == '/products' || CURRENT_ROUTE == '/product/edit') ? 'active' : '' ?>">
            <img src="<?= BASE_URL ?>/assets/products_icon.png" alt="Products">
            <p>Products</p>
        </a>
        
        <a href="<?= BASE_URL ?>/stock-movement" class="<?= (CURRENT_ROUTE == '/stock-movement') ? 'active' : '' ?>">
            <img src="<?= BASE_URL ?>/assets/move_icon.png" alt="Stock Movements">
            <p>Stock Movements</p>
        </a>
        
        <a href="<?= BASE_URL ?>/reports/low_stock" class="<?= (CURRENT_ROUTE == '/reports' || CURRENT_ROUTE == '/reports/low_stock' || CURRENT_ROUTE == '/reports/valuation' || CURRENT_ROUTE == '/reports/movement_ledger' || CURRENT_ROUTE == '/reports/top_movers') ? 'active' : '' ?>">
            <img src="<?= BASE_URL ?>/assets/report_icon.png" alt="Reports">
            <p>Reports</p>
        </a>
        
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="<?= BASE_URL ?>/profile" class="<?= (CURRENT_ROUTE == '/profile') ? 'active' : '' ?>">
            <img src="<?= BASE_URL ?>/assets/profile_icon.png" alt="Profile">
            <p>Profile</p>
        </a>
        <?php endif; ?>
    </nav>

    <div class="user-panel">
        <div class="user">
            <div class="profile">
                <img src="<?= BASE_URL ?>/assets/user_icon.png" class="prism_image" alt="User">
            </div>
            <div class="status">
                <p><?= htmlspecialchars($_SESSION['username'] ?? 'User', ENT_QUOTES, 'UTF-8') ?></p>
                <p id="admin"><?= ucfirst(htmlspecialchars($_SESSION['role'] ?? 'Staff', ENT_QUOTES, 'UTF-8')) ?></p>
            </div>
        </div>
        <a href="<?= BASE_URL ?>/logout">
            <img src="<?= BASE_URL ?>/assets/logout_icon.png" class="logout_image" alt="Logout">
            <p id="logout">Logout</p>
        </a>
    </div>
</aside>

<div class="main">
        <div class="header">
            <div class="left">
                <div><img src="<?= BASE_URL ?>/assets/sidebar_icon.png" alt="Menu"></div>
                <p>PRISM | Inventory Management</p>
            </div>
            <div class="right">
                <div class="role">
                    <p><?= ucfirst(htmlspecialchars($_SESSION['role'] ?? 'Staff', ENT_QUOTES, 'UTF-8')) ?></p>
                </div>
                <div class="user">
                    <p><?= htmlspecialchars($_SESSION['username'] ?? 'User Name', ENT_QUOTES, 'UTF-8') ?></p>
                </div> 
            </div>
        </div>