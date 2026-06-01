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

                            <button type="button" class="btn-new" onclick="openModal('addProductOverlay')"> 
                                <img src="<?= BASE_URL ?>/assets/add_white.png" class="add_image" alt="Add">New Product
                            </button>
                        </div>
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
                                        <button type="button" style="background:none; border:none; cursor:pointer; padding: 0;" onclick="openEditModal(
                                            <?= (int)($row['id'] ?? 0) ?>, 
                                            '<?= htmlspecialchars(addslashes($row['sku'] ?? '')) ?>', 
                                            '<?= htmlspecialchars(addslashes($row['name'] ?? '')) ?>', 
                                            '<?= htmlspecialchars(addslashes($row['description'] ?? '')) ?>', 
                                            '<?= htmlspecialchars(addslashes($row['category_id'] ?? '')) ?>', 
                                            <?= (float)($row['unit_cost'] ?? 0) ?>, 
                                            <?= (float)($row['unit_price'] ?? 0) ?>, 
                                            <?= (int)($row['reorder_threshold'] ?? 0) ?>, 
                                            '<?= htmlspecialchars(addslashes($row['supplier_name'] ?? '')) ?>'
                                        )">
                                            <img src="<?= BASE_URL ?>/assets/edit_icon.png" class="action-img" alt="Edit">
                                        </button>
                                        
                                        <button type="button" style="background:none; border:none; cursor:pointer; padding: 0;" onclick="openInactivateModal(<?= (int)($row['id'] ?? 0) ?>)">
                                            <img src="<?= BASE_URL ?>/assets/delete_icon.png" class="action-img" alt="Delete">
                                        </button>
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

    <div class="prism-overlay" id="addProductOverlay">
        <div class="prism-modal large">
            <div class="modal-header"><h2>Add New Product</h2></div>
            <form action="<?= BASE_URL ?>/products/add" method="POST">
                <div class="form-grid">
                    <div class="form-group"><label>SKU Code *</label><input type="text" name="sku" required></div>
                    <div class="form-group"><label>Product Name *</label><input type="text" name="name" required></div>
                    
                    <div class="form-group full"><label>Description</label><textarea name="description"></textarea></div>
                    
                    <div class="form-group full"><label>Category *</label>
                        <select name="category_id">
                            <option value="" disabled selected>Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group"><label>Unit Cost (₱) *</label><input type="number" step="0.01" name="unit_cost" required></div>
                    <div class="form-group"><label>Selling Price (₱) *</label><input type="number" step="0.01" name="unit_price" required></div>
                    <div class="form-group"><label>Low Stock Threshold *</label><input type="number" name="reorder_threshold" required></div>
                    <div class="form-group"><label>Supplier Name</label><input type="text" name="supplier_name"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel-outline" onclick="closeModal('addProductOverlay')">Cancel</button>
                    <button type="submit" class="btn-save-maroon">Save Product</button>
                </div>
            </form>
        </div>
    </div>

    <div class="prism-overlay" id="editProductOverlay">
        <div class="prism-modal large">
            <div class="modal-header"><h2>Edit Product</h2></div>
            <form action="<?= BASE_URL ?>/products/edit" method="POST">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-grid">
                    <div class="form-group"><label>SKU Code *</label><input type="text" name="sku" id="edit_sku" required readonly style="background:#f9f9f9;"></div>
                    <div class="form-group"><label>Product Name *</label><input type="text" name="name" id="edit_name" required></div>
                    
                    <div class="form-group full"><label>Description</label><textarea name="description" id="edit_desc"></textarea></div>
                    
                    <div class="form-group full"><label>Category *</label>
                        <select name="category_id" id="edit_category" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group"><label>Unit Cost (₱) *</label><input type="number" step="0.01" name="unit_cost" id="edit_cost" required></div>
                    <div class="form-group"><label>Selling Price (₱) *</label><input type="number" step="0.01" name="unit_price" id="edit_price" required></div>
                    <div class="form-group"><label>Low Stock Threshold *</label><input type="number" name="reorder_threshold" id="edit_threshold" required></div>
                    <div class="form-group"><label>Supplier Name</label><input type="text" name="supplier_name" id="edit_supplier"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel-outline" onclick="closeModal('editProductOverlay')">Cancel</button>
                    <button type="submit" class="btn-save-maroon">Update Product</button>
                </div>
            </form>
        </div>
    </div>

    <div class="prism-overlay" id="inactivateOverlay">
        <div class="prism-modal small">
            <h3 class="confirm-title">Confirmation of <span class="text-maroon">Product Inactivity</span></h3>
            <p class="confirm-text">Are you sure you want to mark this<br>product as <strong>"Inactive"</strong>?</p>
            
            <form action="<?= BASE_URL ?>/products/delete" method="POST" class="modal-footer center">
                <input type="hidden" name="id" id="inactivate_id">
                <button type="submit" class="btn-action-solid">Mark Inactive</button>
                <button type="button" class="btn-cancel-solid" onclick="closeModal('inactivateOverlay')">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) { document.getElementById(id).classList.add('show'); }
        function closeModal(id) { document.getElementById(id).classList.remove('show'); }

        // Triggered when clicking "Edit" in the table
        function openEditModal(id, sku, name, desc, cat, cost, price, thresh, supp) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_sku').value = sku;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_desc').value = desc;
            document.getElementById('edit_category').value = cat;
            document.getElementById('edit_cost').value = cost;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_threshold').value = thresh;
            document.getElementById('edit_supplier').value = supp;
            openModal('editProductOverlay');
        }

        // Triggered when clicking "Delete/Inactivate" in the table
        function openInactivateModal(id) {
            document.getElementById('inactivate_id').value = id;
            openModal('inactivateOverlay');
        }
    </script>   
</body>
</html>