<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISM | Movement Ledger</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/global.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/sidebar_header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/movement_ledger.css">
    <style>
        .badge-pill { display: inline-block; padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; text-align: center; border: 1px solid transparent; }
        .badge-in  { background-color: #DBFCE7; color: #15A85A; border-color: #BFF3D4; }
        .badge-out { background-color: #FEE2E2; color: #EF4444; border-color: #FCA5A5; }
        .badge-adjustment { background-color: #FEF3C7; color: #D97706; border-color: #FCD34D; }
        .qty-positive { color: #15A85A; font-weight: 600; }
        .qty-negative { color: #EF4444; font-weight: 600; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/sidebar_header.php'; ?>

    <div class="content">
        <div class="box1">
            <h1 class="page-title">Reports</h1>
            <p class="box1_sub">Insights across your inventory operations.</p>

            <div class="choices">
                <a href="<?= BASE_URL ?>/reports/low_stock"       class="tab-link"><span class="inactive-tab">Low Stock</span></a>
                <a href="<?= BASE_URL ?>/reports/valuation"        class="tab-link"><span class="inactive-tab">Valuation</span></a>
                <a href="<?= BASE_URL ?>/reports/movement_ledger"  class="tab-link"><span class="active">Movement Ledgers</span></a>
                <a href="<?= BASE_URL ?>/reports/top_movers"       class="tab-link"><span class="inactive-tab">Top Movers</span></a>
            </div>

            <form method="GET" action="<?= BASE_URL ?>/reports/movement_ledger" style="margin-top: 25px;">
                <div class="date">
                    <label for="date1" style="margin-right: 5px;">From:</label>
                    <input type="date" name="start_date" id="date1" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>">

                    <label for="date2" style="margin-left: 15px; margin-right: 5px;">To:</label>
                    <input type="date" name="end_date" id="date2" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>">

                    <button type="submit" style="margin-left: 15px; padding: 8px 16px; background: var(--maroon); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Apply Filter</button>
                </div>
            </form>
        </div>

        <div class="box2">
            <div class="box2_left">
                <h4 class="section-title">Movement Ledger Audit</h4>
                <p class="box2_sub">Comprehensive history of all inventory adjustments and transactions.</p>
            </div>
            <div class="box2_right">
                <form method="GET" action="<?= BASE_URL ?>/reports/movement_ledger" class="filter-form">
                    <!-- Preserve date filters when using product/type dropdowns -->
                    <input type="hidden" name="start_date" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>">
                    <input type="hidden" name="end_date"   value="<?= htmlspecialchars($_GET['end_date']   ?? '') ?>">

                    <select name="product" onchange="this.form.submit()">
                        <option value="all" <?= (($filter_product ?? 'all') === 'all') ? 'selected' : '' ?>>All Products</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= htmlspecialchars($p['id']) ?>"
                                <?= (isset($filter_product) && (string)$filter_product === (string)$p['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="movement_type" onchange="this.form.submit()">
                        <option value="all"        <?= (($filter_type ?? 'all') === 'all')        ? 'selected' : '' ?>>All Types</option>
                        <option value="in"         <?= (($filter_type ?? '') === 'in')         ? 'selected' : '' ?>>Stock-In</option>
                        <option value="out"        <?= (($filter_type ?? '') === 'out')        ? 'selected' : '' ?>>Stock-Out</option>
                        <option value="adjustment" <?= (($filter_type ?? '') === 'adjustment') ? 'selected' : '' ?>>Adjustments</option>
                    </select>

                    <a href="<?= BASE_URL ?>/reports/movement_ledger?export=csv&start_date=<?= htmlspecialchars($_GET['start_date'] ?? '') ?>&end_date=<?= htmlspecialchars($_GET['end_date'] ?? '') ?>" class="excel btn-export">
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
                        <th class="text-left">Date &amp; Time</th>
                        <th class="text-left">SKU</th>
                        <th class="text-left">Product Name</th>
                        <th class="text-center">Type</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-left">Recorded By</th>
                        <th class="text-left">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ledger_items)): ?>
                        <?php foreach ($ledger_items as $row):
                            $type_lower = strtolower($row['type']);
                            $is_positive = ($row['type'] === 'IN' || ($row['type'] === 'ADJUSTMENT' && $row['quantity'] > 0));
                            $qty_prefix  = $is_positive ? '+' : '-';
                            $qty_class   = $is_positive ? 'qty-positive' : 'qty-negative';
                        ?>
                            <tr>
                                <td class="text-left" style="white-space:nowrap;">
                                    <?= htmlspecialchars(date('M d, Y h:i A', strtotime($row['created_at']))) ?>
                                </td>
                                <td class="text-left"><strong><?= htmlspecialchars($row['sku']) ?></strong></td>
                                <td class="text-left"><?= htmlspecialchars($row['product_name']) ?></td>
                                <td class="text-center">
                                    <span class="badge-pill badge-<?= $type_lower ?>">
                                        <?= $row['type'] === 'ADJUSTMENT' ? 'Adjustment' : ($row['type'] === 'IN' ? 'Stock-In' : 'Stock-Out') ?>
                                    </span>
                                </td>
                                <td class="text-center <?= $qty_class ?>">
                                    <?= $qty_prefix . htmlspecialchars(abs($row['quantity'])) ?>
                                </td>
                                <td class="text-left"><?= htmlspecialchars($row['username'] ?? 'System') ?></td>
                                <td class="text-left" style="color:#666; font-size:13px; max-width:250px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    <?= htmlspecialchars($row['notes'] ?: '—') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="empty-state">No stock movements have been recorded yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
