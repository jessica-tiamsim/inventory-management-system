<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISM | Stock Movements</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/global.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/stock_movements.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/sidebar_header.css">
</head>
<body>
    <?php include __DIR__ . '/sidebar_header.php'; ?>
    
    <div class="content">
        <div class="page-top-header">
            <div class="header-left">
                <h1>Stock Movements</h1>
                <p class="subtitle">Immutable ledger of every stock change.</p>
                
                <form method="GET" action="<?= BASE_URL ?>/stock-movement" class="filters-row">
                    <select name="sku" id="product" onchange="this.form.submit()">
                        <option value="all" <?= $filter_sku === 'all' ? 'selected' : '' ?>>All Products</option>
                        <?php foreach ($products as $prod): ?>
                            <option value="<?= htmlspecialchars($prod['sku']) ?>" <?= ($filter_sku === $prod['sku']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($prod['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="type" id="type" onchange="this.form.submit()">
                        <option value="all" <?= $filter_type === 'all' ? 'selected' : '' ?>>All Types</option>
                        <option value="IN" <?= $filter_type === 'IN' ? 'selected' : '' ?>>Stock In</option>
                        <option value="OUT" <?= $filter_type === 'OUT' ? 'selected' : '' ?>>Stock Out</option>
                        <option value="ADJUSTMENT" <?= $filter_type === 'ADJUSTMENT' ? 'selected' : '' ?>>Adjustment</option>
                    </select>
                </form>
            </div>
            
            <div class="header-right">
                <button class="btn-record-movement" onclick="openMovementModal()">
                    <img src="<?= BASE_URL ?>/assets/add_red.png" class="add_image" alt="Add"> Record Movement
                </button>
            </div>
        </div>

        <div class="data-card-surface">
            <div class="table-scroll-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Note</th>
                            <th>Recorded By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($movements) > 0): ?>
                            <?php foreach ($movements as $row): 
                                $type_lower = strtolower($row['type']);
                                $qty_prefix = ($row['type'] === 'IN') ? '+' : (($row['type'] === 'OUT') ? '-' : '');
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars(date('n/j/Y g:i:s A', strtotime($row['created_at']))) ?></td>
                                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td>
                                        <span class="badge-pill badge-<?= $type_lower ?>">
                                            <?= htmlspecialchars($row['type'] === 'ADJUSTMENT' ? 'Adjustment' : ($row['type'] === 'IN' ? 'Stock-In' : 'Stock-Out')) ?>
                                        </span>
                                    </td>
                                    <td class="qty-weight"><?= $qty_prefix . htmlspecialchars(abs($row['quantity'])) ?></td>
                                    <td class="note-text-dim"><?= htmlspecialchars($row['notes'] ?: '—') ?></td>
                                    <td><?= htmlspecialchars($row['username'] ?? 'System') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="empty-fallback-row" style="text-align: center; padding: 30px; color: #888;">
                                    📦 No stock transaction logs recorded matching criteria selection.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination-container">
                    <?php 
                    for ($i = 1; $i <= $total_pages; $i++): 
                        $url_params = $_GET;
                        $url_params['page'] = $i;
                        $query_string = http_build_query($url_params);
                        $active_class = ($i === $page) ? 'active-page' : '';
                    ?>
                        <a href="?<?= $query_string ?>" class="page-step <?= $active_class ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="movementModal" class="modal-backdrop">
        <div class="modal-surface-box">
            <div class="modal-top-bar">
                <h2>Record Stock Movement</h2>
                <button class="close-dismiss-btn" onclick="closeMovementModal()">&times;</button>
            </div>
            <form action="<?= BASE_URL ?>/stock-movement/add" method="POST" id="movementForm">
                <div class="field-block">
                    <label>Select Product *</label>
                    <select name="product_id" required>
                        <option value="" disabled selected>-- Choose a Product --</option>
                        <?php foreach ($products as $prod): ?>
                            <option value="<?= htmlspecialchars($prod['id']) ?>">
                                <?= htmlspecialchars($prod['name']) ?> (<?= htmlspecialchars($prod['sku']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field-split-row">
                    <div class="field-block">
                        <label>Movement Type *</label>
                        <select name="type" required>
                            <option value="" disabled selected>-- Select Type --</option>
                            <option value="IN">Stock In (+)</option>
                            <option value="OUT">Stock Out (-)</option>
                            <option value="ADJUSTMENT">Adjustment (Correction)</option>
                        </select>
                    </div>
                    <div class="field-block">
                        <label>Quantity *</label>
                        <input type="number" name="quantity" min="1" placeholder="e.g. 50" required>
                    </div>
                </div>
                <div class="field-block">
                    <label>Reason / Notes</label>
                    <textarea name="notes" placeholder="Provide context metadata..."></textarea>
                </div>
                <div class="form-action-buttons">
                    <button type="button" class="cancel-btn" onclick="closeMovementModal()">Cancel</button>
                    <button type="submit" class="save-btn">Save Transaction</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openMovementModal() { 
        document.getElementById('movementModal').style.display = 'flex'; 
    }
    function closeMovementModal() { 
        document.getElementById('movementModal').style.display = 'none'; 
        document.getElementById('movementForm').reset();
    }
    window.onclick = function(e) { 
        if(e.target === document.getElementById('movementModal')) closeMovementModal(); 
    }
    </script>
</body>
</html>