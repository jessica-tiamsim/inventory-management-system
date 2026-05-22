<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISM | Reports</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/global.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/low_stock.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/sidebar_header.css">
    <link href='https://fonts.googleapis.com/css?family=Inter:wght@400;500;600;700&display=swap' rel='stylesheet'>
</head>
<body>
    <?php include __DIR__ . '/sidebar_header.php'; ?>

    <div class="content">
        <div class="box1">
            <h1 class="page-title">Reports</h1>
            <p class="box1_sub">Insights across your inventory operations.</p>
            
            <div class="choices">
                <a href="<?= BASE_URL ?>/reports/low_stock" class="tab-link"><span class="active">Low Stock</span></a>
                <a href="<?= BASE_URL ?>/reports/valuation" class="tab-link"><span class="inactive-tab">Valuation</span></a>
                <a href="<?= BASE_URL ?>/reports/movement_ledger" class="tab-link"><span class="inactive-tab">Movement Ledgers</span></a>
                <a href="<?= BASE_URL ?>/reports/top_movers" class="tab-link"><span class="inactive-tab">Tops Movers</span></a>
            </div>
        </div>

        <div class="box2">
            <div class="box2_left">
                <h4 class="section-title">Low Stock Report</h4>
                <p class="box2_sub">Active products at or below their reorder threshold</p>
            </div>
            <div class="box2_right">
                <form method="GET" action="<?= BASE_URL ?>/reports/low_stock" class="filter-form">
                    <select name="category" id="product" onchange="this.form.submit()">
                        <option value="all" <?= (isset($_GET['category']) && $_GET['category'] === 'all') ? 'selected' : '' ?>>All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <a href="<?= BASE_URL ?>/reports/low_stock?export=csv<?= isset($_GET['category']) ? '&category='.$_GET['category'] : '' ?>" class="excel btn-export">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        CSV
                    </a>
                </form>
            </div>
        </div>

        <div class="box3">
            <table>
                <thead>
                    <tr>
                        <th class="text-left">SKU</th>
                        <th class="text-left">Product Name</th>
                        <th class="text-left">Category</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-center">Reorder Threshold</th>
                        <th class="text-center">Supplier Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($low_stock_items) > 0): ?>
                        <?php foreach ($low_stock_items as $item): ?>
                            <tr>
                                <td class="text-left"><strong><?= htmlspecialchars($item['sku']) ?></strong></td>
                                <td class="text-left"><?= htmlspecialchars($item['name']) ?></td>
                                <td class="text-left"><?= htmlspecialchars($item['category_name'] ?? 'Uncategorized') ?></td>
                                <td class="text-center" style="color: #801B32; font-weight: 600;">
                                    <?= htmlspecialchars($item['current_qty']) ?>
                                </td>
                                <td class="text-center">
                                    <?= htmlspecialchars($item['reorder_threshold']) ?>
                                </td>
                                <td class="text-center"><?= htmlspecialchars($item['supplier_name'] ?: '—') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="empty-state">
                                ✅ All stock levels are healthy! No items are currently below their reorder threshold.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>