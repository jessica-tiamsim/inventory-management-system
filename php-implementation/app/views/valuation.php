<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISM | Valuation Report</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/global.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/valuation.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/sidebar_header.css">
    <link href='https://fonts.googleapis.com/css?family=Inter:wght@400;500;600;700&display=swap' rel='stylesheet'>
</head>
<body>
    <?php include __DIR__ . '/sidebar_header.php'; ?>

    <div class="content">
        <div class="page-header">
            <h2>Reports</h2>
            <p>Insights across your inventory operations.</p>
            
            <div class="report-tabs" style="margin-top: 15px;">
                <button class="tab-btn" onclick="location.href='<?= BASE_URL ?>/reports/low_stock'">Low Stock</button>
                <button class="tab-btn active" onclick="location.href='<?= BASE_URL ?>/reports/valuation'">Valuation</button>
                <button class="tab-btn" onclick="location.href='<?= BASE_URL ?>/reports/movement_ledger'">Movement Ledgers</button>
                <button class="tab-btn" onclick="location.href='<?= BASE_URL ?>/reports/top_movers'">Tops Movers</button>
            </div>
        </div>

        <div class="report-card">
            
            <div class="report-card-header">
                <div class="report-info">
                    <h3>Inventory Valuation</h3>
                    <p>Sum of (unit cost x current quantity) per category</p>
                </div>
                
                <div class="report-actions">
                    <form method="GET" action="<?= BASE_URL ?>/reports/valuation" style="display:flex; gap:10px; align-items:center;">
                        <select name="sort" onchange="this.form.submit()">
                            <option value="value" <?= (isset($_GET['sort']) && $_GET['sort'] === 'value') ? 'selected' : '' ?>>Sort by Value</option>
                            <option value="category" <?= (isset($_GET['sort']) && $_GET['sort'] === 'category') ? 'selected' : '' ?>>Sort by Category</option>
                        </select>
                        
                        <a href="<?= BASE_URL ?>/reports/valuation?export=csv" class="csv-btn" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/></svg>
                            CSV
                        </a>
                    </form>
                </div>
            </div>

            <div class="report-table">
                <table>
                    <thead>
                        <tr>
                            <th style="border-top-left-radius: 10px;">Category</th>
                            <th style="text-align: right; border-top-right-radius: 10px;">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($valuation_items) > 0): ?>
                            <?php foreach ($valuation_items as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['category_name'] ?? 'Uncategorized') ?></td>
                                    <td style="text-align: right;">Php <?= number_format($item['total_value'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" style="text-align: center; padding: 40px; color: #777;">
                                    No active products found in the database.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td style="text-align: right; color: #1A1714;">Php <?= number_format($grand_total, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
        </div>
    </div>
</body>
</html>