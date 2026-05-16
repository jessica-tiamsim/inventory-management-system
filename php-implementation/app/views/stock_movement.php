<?php
// ==========================================
// 1. SESSION MANAGEMENT & ACCESS CONTROL GUARD
// ==========================================
session_start();

// Strict Requirement: Enforce script-level authentication check
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../../public/index.php?error=unauthorized");
    exit();
}

// ==========================================
// 2. ENVIRONMENT CONFIGURATION & DEPENDENCIES
// ==========================================
require_once __DIR__ . '/../../vendor/autoload.php';

// Safe load for system variables
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../'); 
    $dotenv->load();
}

// Pull central PDO MySQL engine reference
require_once __DIR__ . '/../models/db_connection.php';

// ==========================================
// 3. CAPTURE FILTER PARAMETERS & PAGINATION
// ==========================================
$filter_sku = $_GET['sku'] ?? 'all';
$filter_type = $_GET['type'] ?? 'all';

$limit = 10; // Group One constraint rule limit
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISM - Stock Movements</title>
    <link rel="stylesheet" href="../../public/css/stock_movement.css">
    <link rel="stylesheet" href="../../public/css/sidebar_header.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
</head>
<body>

    <?php include __DIR__ . '/sidebar_header.php'; ?>

    <div class="content">
        <div class="box1">
            <div class="title-group">
                <span class="pageTitle">Stock Movements</span>
                <p class="pageSubtitle">
                    Immutable ledger of stock change.
                </p>
                
                <form method="GET" action="" class="dropdowns">
                    <select name="sku" id="product" onchange="this.form.submit()">
                        <option value="all" <?= $filter_sku === 'all' ? 'selected' : '' ?>>All Products</option>
                        <?php
                        $prod_stmt = $pdo->query("SELECT sku, name FROM products ORDER BY name ASC");
                        while ($prod = $prod_stmt->fetch(PDO::FETCH_ASSOC)) {
                            $selected = ($filter_sku === $prod['sku']) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($prod['sku']) . "' $selected>" . htmlspecialchars($prod['name']) . " (" . htmlspecialchars($prod['sku']) . ")</option>";
                        }
                        ?>
                    </select>

                    <select name="type" id="type" onchange="this.form.submit()">
                        <option value="all" <?= $filter_type === 'all' ? 'selected' : '' ?>>All Types</option>
                        <option value="IN" <?= $filter_type === 'IN' ? 'selected' : '' ?>>Stock In</option>
                        <option value="OUT" <?= $filter_type === 'OUT' ? 'selected' : '' ?>>Stock Out</option>
                        <option value="ADJUSTMENT" <?= $filter_type === 'ADJUSTMENT' ? 'selected' : '' ?>>Adjustment</option>
                    </select>
                </form>
            </div>

            <button class="record" onclick="openMovementModal()">
                + Record Movement
            </button>
        </div>

        <div class="box2">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>DATE & TIME</th>
                            <th>SKU</th>
                            <th>PRODUCT NAME</th>
                            <th>MOVEMENT TYPE</th>
                            <th>QUANTITY CHANGE</th>
                            <th>PERFORMED BY</th>
                            <th>REASON / NOTES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Build execution arrays safe from manipulation
                        $conditions = [];
                        $params = [];

                        if ($filter_sku !== 'all') {
                            $conditions[] = "m.product_sku = :sku";
                            $params['sku'] = $filter_sku;
                        }
                        if ($filter_type !== 'all') {
                            $conditions[] = "m.type = :type";
                            $params['type'] = $filter_type;
                        }

                        $where_clause = count($conditions) > 0 ? "WHERE " . implode(" AND ", $conditions) : "";

                        // Resolve dynamic count metrics for multi-page mapping
                        $count_sql = "SELECT COUNT(*) FROM stock_movements m $where_clause";
                        $count_stmt = $pdo->prepare($count_sql);
                        $count_stmt->execute($params);
                        $total_rows = $count_stmt->fetchColumn();
                        $total_pages = ceil($total_rows / $limit);

                        // Primary SELECT query mapping: Inner Joins eliminate tracking voids
                        $sql = "SELECT m.created_at, m.product_sku, p.name AS product_name, m.type, m.quantity, u.username, m.notes 
                                FROM stock_movements m
                                JOIN products p ON m.product_sku = p.sku
                                JOIN users u ON m.user_id = u.id
                                $where_clause
                                ORDER BY m.created_at DESC 
                                LIMIT :limit OFFSET :offset";

                        $stmt = $pdo->prepare($sql);
                        
                        foreach ($params as $key => $val) {
                            $stmt->bindValue(":$key", $val);
                        }
                        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                        $stmt->execute();
                        $movements = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // Loop out records safely
                        if (count($movements) > 0):
                            foreach ($movements as $row): 
                                $type_class = 'badge-' . strtolower($row['type']);
                                $qty_prefix = ($row['type'] === 'IN') ? '+' : (($row['type'] === 'OUT') ? '-' : '');
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars(date('M d, Y h:i A', strtotime($row['created_at']))) ?></td>
                                    <td><strong><?= htmlspecialchars($row['product_sku']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td><span class="badge <?= $type_class ?>"><?= htmlspecialchars($row['type']) ?></span></td>
                                    <td class="<?= $type_class ?>-text">
                                        <strong><?= $qty_prefix . htmlspecialchars($row['quantity']) ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                    <td><span class="notes-text"><?= htmlspecialchars($row['notes'] ?: 'N/A') ?></span></td>
                                </tr>
                            <?php 
                            endforeach;
                        else: 
                        ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: #888; padding: 40px;">
                                    No transaction records found matching active selection constraints.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php 
                    for ($i = 1; $i <= $total_pages; $i++): 
                        $url_params = $_GET;
                        $url_params['page'] = $i;
                        $query_string = http_build_query($url_params);
                        $active_class = ($i === $page) ? 'active' : '';
                    ?>
                        <a href="?<?= $query_string ?>" class="page-link <?= $active_class ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="movementModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <h3>Record Stock Movement</h3>
                <span class="modal-close" onclick="closeMovementModal()">&times;</span>
            </div>
            
            <form action="../controllers/record_movement_controller.php" method="POST" id="movementForm">
                <div class="form-group">
                    <label for="modal_sku">Select Product *</label>
                    <select name="sku" id="modal_sku" required>
                        <option value="" disabled selected>-- Choose a Product --</option>
                        <?php
                        // Limit selection to active inventory components
                        $modal_prod_stmt = $pdo->query("SELECT sku, name FROM products WHERE status = 'active' ORDER BY name ASC");
                        while ($prod = $modal_prod_stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='" . htmlspecialchars($prod['sku']) . "'>" . htmlspecialchars($prod['name']) . " (" . htmlspecialchars($prod['sku']) . ")</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="modal_type">Movement Type *</label>
                    <select name="type" id="modal_type" required>
                        <option value="" disabled selected>-- Choose Action Type --</option>
                        <option value="IN">Stock In (+ Increases Inventory)</option>
                        <option value="OUT">Stock Out (- Decreases Inventory)</option>
                        <option value="ADJUSTMENT">Adjustment (Correction)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="modal_quantity">Quantity *</label>
                    <input type="number" name="quantity" id="modal_quantity" min="1" placeholder="Enter amount (e.g. 50)" required>
                </div>

                <div class="form-group">
                    <label for="modal_notes">Reason / Notes</label>
                    <textarea name="notes" id="modal_notes" rows="3" placeholder="Provide context regarding this update..."></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeMovementModal()">Cancel</button>
                    <button type="submit" class="btn-gold-submit">Save Transaction</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openMovementModal() {
        document.getElementById('movementModal').style.display = 'block';
    }

    function closeMovementModal() {
        document.getElementById('movementModal').style.display = 'none';
        document.getElementById('movementForm').reset();
    }

    // Dismiss window framework if background shield is chosen
    window.onclick = function(event) {
        let modal = document.getElementById('movementModal');
        if (event.target === modal) {
            closeMovementModal();
        }
    }
    </script>
</body>
</html>