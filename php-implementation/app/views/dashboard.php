<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISM | Inventory Management</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/global.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/dashboard.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/sidebar_header.css">
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
                            <?php foreach ($low_items as $item): 
                                
                                // 1. Convert to lowercase for easy matching
                                $category = strtolower($item['category_name'] ?? '');
                                
                                // 2. The default fallback image if a category is missing
                                $icon_file = 'default_icon.png'; 
                                
                                // 3. Match your exact PRISM categories
                                if (strpos($category, 'meat') !== false) {
                                    $icon_file = 'meat_icon.png';
                                } elseif (strpos($category, 'seafood') !== false) {
                                    $icon_file = 'seafood_icon.png';
                                } elseif (strpos($category, 'vegetables') !== false) {
                                    $icon_file = 'vegetables_icon.png';
                                } elseif (strpos($category, 'fruits') !== false) {
                                    $icon_file = 'fruits_icon.png';
                                } elseif (strpos($category, 'dairy') !== false) {
                                    $icon_file = 'dairy_icon.png';
                                } elseif (strpos($category, 'dry goods') !== false) {
                                    $icon_file = 'dry_goods_icon.png';
                                } elseif (strpos($category, 'beverages') !== false) {
                                    $icon_file = 'beverages_icon.png';
                                } elseif (strpos($category, 'frozen') !== false) {
                                    $icon_file = 'frozen_icon.png';
                                } elseif (strpos($category, 'condiments') !== false || strpos($category, 'sauces') !== false) {
                                    $icon_file = 'condiments_icon.png';
                                } elseif (strpos($category, 'spices') !== false || strpos($category, 'seasonings') !== false) {
                                    $icon_file = 'spices_icon.png';
                                }
                            ?>
                                <li class="list-item alert-item">
                                    <div class="item-info">
                                        <span class="warning-icon" title="Category: <?= htmlspecialchars($item['category_name'] ?? 'Uncategorized') ?>">
                                            <img src="<?= BASE_URL ?>/assets/<?= $icon_file ?>" class="cat-img-icon" alt="Icon">
                                        </span>
                                        
                                        <div class="item-text">
                                            <strong><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                                            <small class="sku-text"><?= htmlspecialchars($item['sku'], ENT_QUOTES, 'UTF-8') ?></small>
                                        </div>
                                    </div>
                                    <div class="qty-badge">
                                        <span class="current-qty"><?= (int)$item['current_qty'] ?> left</span>
                                        <span class="threshold-qty">/ <?= (int)$item['reorder_threshold'] ?> min</span>
                                    </div>
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
                                // Determine the wording based on the movement type
                                $type = strtolower($act['movement_type']);
                                $direction_text = 'Adjusted';
                                $badge_text = 'ADJ';
                                
                                if ($type === 'in') {
                                    $direction_text = 'Added';
                                    $badge_text = 'IN';
                                } elseif ($type === 'out') {
                                    $direction_text = 'Removed';
                                    $badge_text = 'OUT';
                                }
                            ?>
                                <li class="list-item">
                                    <div class="item-info">
                                        <span class="badge-pill badge-<?= $type ?>"><?= $badge_text ?></span>
                                        <div class="item-text">
                                            <span><?= $direction_text ?> <strong><?= $act['quantity'] ?></strong> units</span>
                                            <small class="sku-text"><?= htmlspecialchars($act['name'], ENT_QUOTES, 'UTF-8') ?></small>
                                        </div>
                                    </div>
                                    <small class="time-stamp" style="color: var(--gray); font-size: 11px;">
                                        <?= date('M d, h:i A', strtotime($act['created_at'])) ?>
                                    </small>
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