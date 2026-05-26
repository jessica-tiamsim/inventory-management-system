<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISM | Inventory Management</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/global.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/dashboard.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/sidebar_header.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
</head>
<body>
    <?php include __DIR__ . '/sidebar_header.php'; ?>

    <div class="content">
        <div class="dashboard-container">
            <h1 class="welcome-text">Welcome back, <?= htmlspecialchars($user_display_name, ENT_QUOTES, 'UTF-8') ?>!</h1>

            <div class="stats-grid">
                <div class="stat-card blue-border">
                    <p class="card-label">ACTIVE PRODUCTS</p>
                    <h2 class="card-value color-blue"><?= $active_count ?></h2>
                    <p class="card-sub"><?= $inactive_count ?> Inactive</p>
                </div>
                <div class="stat-card red-border">
                    <p class="card-label">UNITS IN STOCK</p>
                    <h2 class="card-value color-red"><?= number_format($total_units) ?></h2>
                    <p class="card-sub">across all SKUs</p>
                </div>
                <div class="stat-card blue-border">
                    <p class="card-label">INVENTORY VALUE</p>
                    <h2 class="card-value color-blue">₱<?= number_format($inventory_value, 2) ?></h2>
                    <p class="card-sub">at unit cost</p>
                </div>
                <div class="stat-card green-border">
                    <p class="card-label">LOW STOCK ALERTS</p>
                    <h2 class="card-value color-green"><?= $low_stock_count ?></h2>
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
                        <button class="view-report-btn" onclick="location.href='<?= BASE_URL ?>/reports'">View Report</button>
                    </div>
                    <div class="list-container">
                        <?php if (count($low_items) > 0): ?>
                            <ul class="dashboard-list">
                                <?php foreach ($low_items as $item): ?>
                                    <li class="list-item alert-item">
                                        <span><strong><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></strong> (<?= htmlspecialchars($item['sku'], ENT_QUOTES, 'UTF-8') ?>)</span>
                                        <span class="badged-alert-text">Qty: <?= $item['current_qty'] ?> / Min: <?= $item['reorder_threshold'] ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="placeholder-text">✅ All stock levels are completely healthy.</p>
                        <?php endif; ?>
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
                        <?php if (count($activities) > 0): ?>
                            <ul class="dashboard-list">
                                <?php foreach ($activities as $act): 
                                    $direction_text = ($act['movement_type'] === 'in') ? 'Added' : (($act['movement_type'] === 'out') ? 'Removed' : 'Adjusted');
                                    $badge_modifier = strtolower($act['movement_type']);
                                ?>
                                    <li class="list-item">
                                        <div>
                                            <span class="activity-badge badge-<?= $badge_modifier ?>"><?= strtoupper($act['movement_type']) ?></span>
                                            <span><?= $direction_text ?> <strong><?= $act['quantity'] ?></strong> units of <?= htmlspecialchars($act['name'], ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                        <small class="time-stamp"><?= date('M d, h:i A', strtotime($act['created_at'])) ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="placeholder-text">No recent stock activity found in the system ledger.</p>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </div>
    </div>
</body>
</html>