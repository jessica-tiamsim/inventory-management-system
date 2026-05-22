<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISM | Movement Ledger</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/global.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/movement_ledger.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/sidebar_header.css">
    <link href='https://fonts.googleapis.com/css?family=Inter:wght@400;500;600;700&display=swap' rel='stylesheet'>
    <style>
        /* Re-injecting the badge pill styles specifically for the ledger */
        .badge-pill { display: inline-block; padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; text-align: center; border: 1px solid transparent; }
        .badge-in { background-color: #DBFCE7; color: #15A85A; border-color: #BFF3D4; }
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
                <a href="<?= BASE_URL ?>/reports/low_stock" class="tab-link"><span class="inactive-tab">Low Stock</span></a>
                <a href="<?= BASE_URL ?>/reports/valuation" class="tab-link"><span class="inactive-tab">Valuation</span></a>
                <a href="<?= BASE_URL ?>/reports/movement_ledger" class="tab-link"><span class="active">Movement Ledgers</span></a>
                <a href="<?= BASE_URL ?>/reports/top_movers" class="tab-link"><span class="inactive-tab">Tops Movers</span></a>
            </div>
            <form method="GET" action="<?= BASE_URL ?>/reports/top_movers" style="margin-top: 25px;">
                <div class="date">
                    <label for="date1" style="margin-right: 5px;">From:</label>
                    <input type="date" name="start_date" id="date1" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>">
                    
                    <label for="date2" style="margin-left: 15px; margin-right: 5px;">To:</label>
                    <input type="date" name="end_date" id="date2" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>">
                </div>
        </div>

        <div class="box2">
            <div class="box2_left">
                <h4 class="section-title">Movement Ledger Audit</h4>
                <p class="box2_sub">Comprehensive history of all inventory adjustments and transactions.</p>
            </div>
        </div>

        <div class="box3">
            <table>
                <thead>
                    <tr class="header-box">
                        <th class="text-left">Date & Time</th>
                        <th class="text-left">SKU</th>
                        <th class="text-left">Product Name</th>
                        <th class="text-center">Type</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-left">Recorded By</th>
                        <th class="text-left">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($ledger_items) > 0): ?>
                        <?php foreach ($ledger_items as $row): 
                            $type_lower = strtolower($row['type']);
                            $qty_prefix = ($row['type'] === 'IN' || ($row['type'] === 'ADJUSTMENT' && $row['quantity'] > 0)) ? '+' : '-';
                            $qty_class = ($qty_prefix === '+') ? 'qty-positive' : 'qty-negative';
                        ?>
                            <tr>
                                <td class="text-left" style="white-space: nowrap;"><?= htmlspecialchars(date('M d, Y h:i A', strtotime($row['created_at']))) ?></td>
                                <td class="text-left"><strong><?= htmlspecialchars($row['sku']) ?></strong></td>
                                <td class="text-left"><?= htmlspecialchars($row['product_name']) ?></td>
                                <td class="text-center">
                                    <span class="badge-pill badge-<?= $type_lower ?>">
                                        <?= htmlspecialchars($row['type'] === 'ADJUSTMENT' ? 'Adjustment' : ($row['type'] === 'IN' ? 'Stock-In' : 'Stock-Out')) ?>
                                    </span>
                                </td>
                                <td class="text-center <?= $qty_class ?>">
                                    <?= $qty_prefix . htmlspecialchars(abs($row['quantity'])) ?>
                                </td>
                                <td class="text-left"><?= htmlspecialchars($row['username'] ?? 'System') ?></td>
                                <td class="text-left" style="color: #666; font-size: 13px; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= htmlspecialchars($row['notes'] ?: '—') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="empty-state">
                                No stock movements have been recorded yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>