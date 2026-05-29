<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISM | Product Catalog</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/global.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/products.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/sidebar_header.css">
</head>
<body>
    <?php include __DIR__ . '/sidebar_header.php'; ?>
    
    <div class="content">
        <div class="content-box">
            <div class="content-header">
                <div>
                    <h1>Products</h1>
                    <p>Manage Your Catalog and Stock Threshold.</p>
                </div>
                <div class="content-lower">
                    <div class="filters-bar">
                        <div class="filters-bar">
                            <form method="GET" action="<?= BASE_URL ?>/products" style="display: flex; gap: 15px; align-items: center; margin: 0; width: 100%;">
                                
                                <div class="search-wrapper">
                                    <input type="text" name="search" placeholder="Search by Product Name or SKU" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                    
                                    <button type="submit" class="searchBtn">
                                        <img class="prism_image" src="<?= BASE_URL ?>/assets/searchbar_icon.png" alt="Search">
                                    </button>
                                </div>

                                <select name="category_filter" onchange="this.form.submit()">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= htmlspecialchars($cat['id']) ?>" <?= (isset($_GET['category_filter']) && $_GET['category_filter'] == $cat['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <select name="status_filter" onchange="this.form.submit()">
                                    <option value="2" <?= (!isset($_GET['status_filter']) || $_GET['status_filter'] == '2') ? 'selected' : '' ?>>All Status</option>
                                    <option value="1" <?= (isset($_GET['status_filter']) && $_GET['status_filter'] == '1') ? 'selected' : '' ?>>Active</option>
                                    <option value="0" <?= (isset($_GET['status_filter']) && $_GET['status_filter'] == '0') ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </form>

                            <button type="button" class="btn-new" onclick="openAddProductModal()"> 
                                <img src="<?= BASE_URL ?>/assets/add_white.png" class="add_image" alt="Add">New Product
                            </button>
                        </div>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>PRODUCT NAME</th>
                            <th>DESCRIPTION</th>
                            <th>CATEGORY</th>
                            <th>UNIT PRICE</th>
                            <th>UNIT COST</th>
                            <th>REORDER THRESHOLD</th>
                            <th>STATUS</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($products) > 0): ?>
                            <?php foreach ($products as $row): 
                                $status_text = ((int)$row['is_active'] === 1) ? 'Active' : 'Inactive';
                                $status_class = ((int)$row['is_active'] === 1) ? 'status-active' : 'status-inactive';
                            ?>
                                <tr>
                                    <td><code><?= htmlspecialchars($row['sku']) ?></code></td>
                                    <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['description']) ?></td>
                                    <td><?= htmlspecialchars($row['category_name'] ?? 'Uncategorized') ?></td>
                                    <td>₱<?= number_format($row['unit_price'], 2) ?></td>
                                    <td>₱<?= number_format($row['unit_cost'], 2) ?></td>
                                    <td><?= number_format((int)$row['reorder_threshold']) ?></td>
                                    <td><span class="<?= $status_class ?>"><?= $status_text ?></span></td>
                                    <td>
                                        <a href="<?= BASE_URL ?>/products/edit?id=<?= urlencode((int)$row['id']) ?>">
                                            <img src="<?= BASE_URL ?>/assets/edit_icon.png" class="action-img" alt="Edit">
                                        </a>
                                        <a href="<?= BASE_URL ?>/products/delete?id=<?= urlencode((int)$row['id']) ?>" onclick="return confirm('Are you sure you want to delete this product?');">
                                            <img src="<?= BASE_URL ?>/assets/delete_icon.png" class="action-img" alt="Delete">
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 40px; color: #a0aec0;">
                                    📦 No products found in the database. Click "+ New Product" to add one!
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="addProductModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <h2>Add New Product</h2>
                <button class="close-btn" onclick="closeAddProductModal()">&times;</button>
            </div>
            
            <form method="POST" action="<?= BASE_URL ?>/products/add">
                <div class="form-grid">
                    <div class="input-group">
                        <label for="sku">SKU Code *</label>
                        <input type="text" id="sku" name="sku" placeholder="e.g., BEV-002" required>
                    </div>
                    <div class="input-group">
                        <label for="product_name">Product Name *</label>
                        <input type="text" id="product_name" name="name" placeholder="e.g., Pepsi" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Optional details..."></textarea>
                </div>

                <div class="input-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['id']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-grid">
                    <div class="input-group">
                        <label for="unit_cost">Unit Cost (₱) *</label>
                        <input type="number" id="unit_cost" name="unit_cost" step="0.01" min="0" placeholder="0.00" required>
                    </div>
                    <div class="input-group">
                        <label for="unit_price">Selling Price (₱) *</label>
                        <input type="number" id="unit_price" name="unit_price" step="0.01" min="0" placeholder="0.00" required>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="input-group">
                        <label for="reorder_threshold">Low Stock Threshold *</label>
                        <input type="number" id="reorder_threshold" name="reorder_threshold" min="0" value="10" required>
                    </div>
                    <div class="input-group">
                        <label for="supplier_name">Supplier Name</label>
                        <input type="text" id="supplier_name" name="supplier_name" placeholder="e.g., Asia Brewery">
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeAddProductModal()">Cancel</button>
                    <button type="submit" class="btn-save">Save Product</button>
                </div>
            </form>
        </div>
    </div>

</div> 
<script>
function openAddProductModal() {
    document.getElementById('addProductModal').style.display = 'flex';
}

function closeAddProductModal() {
    document.getElementById('addProductModal').style.display = 'none';
}

window.onclick = function(event) {
    let modal = document.getElementById('addProductModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
</script>
</body>
</html>