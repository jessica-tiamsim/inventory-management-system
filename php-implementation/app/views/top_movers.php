<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISM | Top Movers</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/global.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/top_movers.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/sidebar_header.css">
    <link href='https://fonts.googleapis.com/css?family=Inter:wght@400;500;600;700&display=swap' rel='stylesheet'>
</head>
<body>
    <?php include __DIR__ . '/sidebar_header.php'; ?>

    <div class="content">
        <div class="box1" style="height: auto; padding-bottom: 20px;">
            <h1 class="page-title">Reports</h1>
            <p class="box1_sub">Insights across your inventory operations.</p>
            
            <div class="choices">
                <a href="<?= BASE_URL ?>/reports/low_stock" class="tab-link"><span class="inactive-tab">Low Stock</span></a>
                <a href="<?= BASE_URL ?>/reports/valuation" class="tab-link"><span class="inactive-tab">Valuation</span></a>
                <a href="<?= BASE_URL ?>/reports/movement_ledger" class="tab-link"><span class="inactive-tab">Movement Ledgers</span></a>
                <a href="<?= BASE_URL ?>/reports/top_movers" class="tab-link"><span class="active">Tops Movers</span></a>
            </div>

            <form method="GET" action="<?= BASE_URL ?>/reports/top_movers" style="margin-top: 25px;">
                <div class="date">
                    <label for="date1" style="margin-right: 5px;">From:</label>
                    <input type="date" name="start_date" id="date1" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>">
                    
                    <label for="date2" style="margin-left: 15px; margin-right: 5px;">To:</label>
                    <input type="date" name="end_date" id="date2" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>">
                </div>
            </form>
        </div>

        <div class="box2">
            <div class="box2_left">
                <h4 class="section-title">Top Movers</h4>
                <p class="box2_sub" style="margin: 0;">Products with the highest stock-out volume in range</p>
            </div>
            <div class="box2_right">
                <a href="<?= BASE_URL ?>/reports/top_movers?export=csv" class="excel btn-export">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    CSV
                </a>
            </div>
        </div>

        <div class="box3">
            <table>
                <thead>
                    <tr class="header-box">
                        <th class="text-center">SKU</th>
                        <th class="text-center">Product Name</th>
                        <th class="text-center">Category</th>
                        <th class="text-center">Units Out</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($top_movers) > 0): ?>
                        <?php foreach ($top_movers as $item): ?>
                            <tr>
                                <td class="text-center"><strong><?= htmlspecialchars($item['sku']) ?></strong></td>
                                <td class="text-center"><?= htmlspecialchars($item['product_name']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($item['category_name'] ?? 'Uncategorized') ?></td>
                                <td class="text-center" style="font-weight: 600; color: #1A1714;">
                                    <?= number_format($item['total_moved']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="empty-state">
                                Not enough data. Start dispatching items to see your top movers here!
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>