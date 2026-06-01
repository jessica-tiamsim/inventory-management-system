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
        <?php if (isset($_GET['error']) && $_GET['error'] === 'insufficient_stock'): ?>
            <div style="background: #FEE2E2; border: 1px solid #FCA5A5; color: #EF4444; padding: 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
                ⚠️ Transaction Failed: Cannot dispatch more stock than you currently have on hand.
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success']) && $_GET['success'] === 'recorded'): ?>
            <div style="background: #DBFCE7; border: 1px solid #BFF3D4; color: #15A85A; padding: 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
                ✅ Stock movement successfully recorded to the ledger!
            </div>
        <?php endif; ?>

        <div class="page-top-header">
            <div class="header-left">
                <h1>Stock Movements</h1>
                <p class="subtitle">Immutable ledger of every stock change.</p>
                
                <form method="GET" action="<?= BASE_URL ?>/stock-movement" class="filters-row" style="margin-top: 15px;">
                    <select name="sku" onchange="this.form.submit()">
                        <option value="all" <?= ($filter_sku ?? 'all') === 'all' ? 'selected' : '' ?>>All Products</option>
                        <?php foreach ($products as $prod): ?>
                            <option value="<?= htmlspecialchars($prod['sku']) ?>" <?= (($filter_sku ?? '') === $prod['sku']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($prod['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="type" onchange="this.form.submit()">
                        <option value="all" <?= ($filter_type ?? 'all') === 'all' ? 'selected' : '' ?>>All Types</option>
                        <option value="IN" <?= ($filter_type ?? '') === 'IN' ? 'selected' : '' ?>>Stock In</option>
                        <option value="OUT" <?= ($filter_type ?? '') === 'OUT' ? 'selected' : '' ?>>Stock Out</option>
                        <option value="ADJUSTMENT" <?= ($filter_type ?? '') === 'ADJUSTMENT' ? 'selected' : '' ?>>Adjustment</option>
                    </select>
                </form>
            </div>
            
            <div class="header-right">
                <button class="btn-record-movement" onclick="openModal('movementModal')">
                    <img src="<?= BASE_URL ?>/assets/add_red.png" class="add_image" alt="Add" style="width: 20px;"> Record Movement
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
                        <?php if (!empty($movements) && count($movements) > 0): ?>
                            <?php foreach ($movements as $row): 
                                $type_lower = strtolower($row['type']);
                                $qty_prefix = ($row['type'] === 'IN' || ($row['type'] === 'ADJUSTMENT' && $row['quantity'] > 0)) ? '+' : '-';
                                $qty_color = ($qty_prefix === '+') ? '#15A85A' : '#EF4444';
                            ?>
                                <tr>
                                    <td style="white-space: nowrap;"><?= htmlspecialchars(date('n/j/Y g:i:s A', strtotime($row['created_at']))) ?></td>
                                    <td><strong><?= htmlspecialchars($row['product_name']) ?></strong></td>
                                    <td>
                                        <span class="badge-pill badge-<?= $type_lower ?>">
                                            <?= htmlspecialchars($row['type'] === 'ADJUSTMENT' ? 'Adjustment' : ($row['type'] === 'IN' ? 'Stock-In' : 'Stock-Out')) ?>
                                        </span>
                                    </td>
                                    <td class="qty-weight" style="color: <?= $qty_color ?>; font-size: 15px;">
                                        <?= $qty_prefix . htmlspecialchars(abs($row['quantity'])) ?>
                                    </td>
                                    <td class="note-text-dim" style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?= htmlspecialchars($row['notes'] ?: '—') ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['username'] ?? 'System') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="empty-fallback-row">
                                    📦 No stock transaction logs recorded matching criteria selection.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php $page = $page ?? 1; ?>
            <?php if (isset($total_pages) && $total_pages > 1): ?>
                <div class="pagination-container" style="padding-bottom: 20px;">
                    <?php 
                        $base_params = $_GET;
                        $prev_page = max(1, $page - 1);
                        $next_page = min($total_pages, $page + 1);
                    ?>
                    <a href="?<?= http_build_query(array_merge($base_params, ['page' => $prev_page])) ?>" class="page-step">Prev</a>
                    <?php 
                    for ($i = 1; $i <= $total_pages; $i++): 
                        $url_params = $_GET;
                        $url_params['page'] = $i;
                        $query_string = http_build_query($url_params);
                        $active_class = ($i === $page) ? 'active-page' : '';
                    ?>
                        <a href="?<?= $query_string ?>" class="page-step <?= $active_class ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <a href="?<?= http_build_query(array_merge($base_params, ['page' => $next_page])) ?>" class="page-step">Next</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="prism-overlay" id="movementModal">
        <div class="prism-modal large">
            <div class="modal-header"><h2>Record Stock Movement</h2></div>
            <form action="<?= BASE_URL ?>/stock-movement/add" method="POST" id="movementForm">
                <div class="form-grid">
                    <div class="form-group full"><label>Select Product *</label>
                        <select name="product_id" required>
                            <option value="" disabled selected>-- Choose a Product --</option>
                            <?php foreach ($products as $prod): ?>
                                <option value="<?= htmlspecialchars($prod['id']) ?>">
                                    <?= htmlspecialchars($prod['name']) ?> (<?= htmlspecialchars($prod['sku']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group"><label>Movement Type *</label>
                        <select name="type" required>
                            <option value="" disabled selected>-- Select Type --</option>
                            <option value="IN">Stock In (+)</option>
                            <option value="OUT">Stock Out (-)</option>
                            <option value="ADJUSTMENT">Adjustment (Correction)</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Quantity *</label>
                        <input type="number" name="quantity" min="1" placeholder="e.g. 50" required>
                    </div>
                    
                    <div class="form-group full"><label>Reason / Notes</label>
                        <textarea name="notes" placeholder="Provide context metadata..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel-outline" onclick="closeModal('movementModal')">Cancel</button>
                    <button type="submit" class="btn-save-maroon">Save Transaction</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) { document.getElementById(id).classList.add('show'); }
        function closeModal(id) { 
            document.getElementById(id).classList.remove('show'); 
            if(id === 'movementModal') document.getElementById('movementForm').reset();
        }
        window.onclick = function(e) { 
            if(e.target === document.getElementById('movementModal')) closeModal('movementModal'); 
        }
    </script>
</body>
</html>