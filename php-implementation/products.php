<?php
session_start();
if (!isset($_SESSION['inventory'])) {
    $_SESSION['inventory'] = [
        ['sku' => 'BEV-001', 'name' => 'Coca-Cola', 'category' => 'Carbonated', 'status' => 'Active']
    ];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sku'])) {
    $newProduct = [
        'sku'      => htmlspecialchars($_POST['sku']),
        'name'     => htmlspecialchars($_POST['name']),
        'category' => htmlspecialchars($_POST['category']),
        'status'   => 'Active'
    ];
    $_SESSION['inventory'][] = $newProduct;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISM | Inventory Management</title>
    <style>
        :root {
            --gold: #FFBF00;
            --maroon: #801B32;
            --maroon-grad: linear-gradient(180deg, #801B32 0%, #5A1424 100%);
            --white: #FFFFFF;
            --bg-gray: #F5F5F5;
            --breadcrumb-gray: #D9D9D9;
            --font-main: 'Inter', 'Roboto', sans-serif;
        }


        body, html { margin: 0; padding: 0; font-family: var(--font-main); height: 100vh; overflow: hidden; }


        #dashboard-container {
            display: grid;
            grid-template-columns: 280px 1fr;
            height: 100vh;
        }

        .sidebar {
            background: var(--maroon-grad);
            color: white;
            display: flex;
            flex-direction: column;
            padding: 40px 0 20px 0;
        }
       
        .sidebar-logo-area { padding: 0 25px 40px 25px; }
        .logo-flex { display: flex; align-items: center; gap: 12px; }
        .logo-img { width: 35px; height: 35px; object-fit: contain; }
        .logo-text-group h2 { margin: 0; font-size: 24px; font-weight: 700; letter-spacing: 0.5px; line-height: 1; }
        .admin-portal-label { margin-top: 4px; color: rgba(255,255,255,0.6); font-size: 13px; }


        .nav-list { padding: 0 0 0 15px; list-style: none; }
        .nav-item {
            display: flex; align-items: center; gap: 15px; padding: 12px 20px;
            text-decoration: none; color: rgba(255,255,255,0.8); font-size: 14px; margin-bottom: 5px;
        }
        .nav-item.active {
            background-color: var(--gold); color: var(--maroon);
            border-radius: 25px 0 0 25px; font-weight: bold;
        }
        .nav-icon { width: 20px; height: 20px; object-fit: contain; }


        .sidebar-footer {
            margin-top: auto;
            padding: 25px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-profile-container {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            color: white;
        }


        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
        }


        .user-text {
            display: flex;
            flex-direction: column;
        }


        .user-name {
            font-size: 14px;
            font-weight: 600;
        }


        .user-role {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
        }


        .logout-button {
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 50px;
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            color: white;
            font-weight: 600;
            font-size: 14px;
            transition: background 0.2s;
        }


        .logout-button:hover {
            background: rgba(255, 255, 255, 0.2);
        }


        .logout-icon-img {
            width: 16px;
            height: 16px;
        }


        .main-body { display: flex; flex-direction: column; background-color: white; }
        .header-top { padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; }
        .breadcrumb-spacer { background-color: var(--breadcrumb-gray); padding: 8px 40px; font-size: 11px; font-weight: bold; color: #333; }
        .body-wrapper { background-color: var(--bg-gray); padding: 30px; flex-grow: 1; display: flex; flex-direction: column; }
        .content-box { background-color: white; border-radius: 12px; padding: 35px; flex-grow: 1; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; flex-direction: column; }


        .content-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px; }
        .content-header h1 { margin: 0; color: var(--maroon); font-size: 32px; font-weight: 700; }
        .content-header p { margin: 5px 0 0; color: #666; font-size: 14px; }


        .btn-new { background-color: var(--maroon); color: white; border: none; padding: 12px 24px; border-radius: 50px; font-weight: bold; display: flex; align-items: center; gap: 8px; cursor: pointer; }


        .table-container { border-radius: 10px; overflow: hidden; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: var(--maroon); color: white; }
        th { padding: 15px 20px; font-size: 12px; text-align: left; }
        td { padding: 18px 20px; font-size: 13px; border-bottom: 1px solid #eee; }
        .status-active { color: #2DCA73; font-weight: 700; }
        .action-img { width: 32px; height: 32px; cursor: pointer; margin-right: 8px; }

        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4); display: none;
            justify-content: center; align-items: center; z-index: 1000;
        }


        .modal-content {
            background: white; width: 750px; border-radius: 16px;
            padding: 40px; position: relative; box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }


        .modal-content h2 { margin: 0 0 5px 0; font-size: 24px; font-weight: 700; }
        .modal-content p { margin: 0 0 25px 0; color: #666; font-size: 14px; }


        .details-section {
            border: 1px solid #EAEAEA; border-radius: 12px;
            padding: 25px; position: relative;
        }


        .details-label {
            font-weight: 700; font-size: 14px; margin-bottom: 20px; display: block;
            position: absolute; top: -10px; left: 20px; background: white; padding: 0 10px;
        }


        .close-btn {
            position: absolute; top: 20px; right: 20px; background: none;
            border: none; font-size: 24px; cursor: pointer; color: #999;
        }


        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: span 2; }


        .form-group label {
            display: block; font-size: 12px; font-weight: 800;
            margin-bottom: 8px; color: #333; text-transform: none;
        }


        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 12px; border-radius: 8px;
            border: 1.5px solid #E8E8E8; font-size: 14px; box-sizing: border-box;
        }


        .form-group textarea { height: 80px; resize: none; }
        .input-hint { font-size: 10px; color: #AAA; margin-top: 5px; display: block; }


        .modal-footer {
            display: flex; justify-content: flex-end; gap: 12px; margin-top: 30px;
        }


        .btn-save { background-color: var(--maroon); color: white; border: none; padding: 12px 30px; border-radius: 8px; font-weight: bold; cursor: pointer; }
        .btn-cancel { background: none; border: 1px solid #DDD; color: #333; padding: 12px 30px; border-radius: 8px; font-weight: bold; cursor: pointer; }


    </style>
</head>
<body>


<div id="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-logo-area">
            <div class="logo-flex">
                <img src="logo.png" class="logo-img" alt="Logo">
                <div class="logo-text-group">
                    <h2>PRISM</h2>
                    <div class="admin-portal-label">Admin Portal</div>
                </div>
            </div>
        </div>


        <nav class="nav-list">
            <a href="#" class="nav-item"><img src="../assets/dash_icon.png" class="nav-icon"> Dashboard</a>
            <a href="#" class="nav-item active"><img src="../assets/products_icon.png" class="nav-icon"> Products</a>
            <a href="#" class="nav-item"><img src="../assets/move_icon.png" class="nav-icon"> Stock Movements</a>
            <a href="#" class="nav-item"><img src="../assets/report_icon.png" class="nav-icon"> Reports</a>
            <a href="#" class="nav-item"><img src="../assets/profile_icon.png" class="nav-icon"> Profile</a>
        </nav>


        <div class="sidebar-footer">
            <div class="user-profile-container">
                <img src="../assets/user_icon.png" alt="Avatar" class="user-avatar">
                <div class="user-text">
                    <span class="user-name">Admin User</span>
                    <span class="user-role">Admin</span>
                </div>
            </div>


            <button class="logout-button">
                <img src="../assets/logout_icon.png" alt="Logout" class="logout-icon-img">
                <span>Logout</span>
            </button>
        </div>
    </aside>


    <main class="main-body">
        <header class="header-top">
            <div style="color: var(--maroon); font-weight: 700;">PRISM | Inventory Management</div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="background: var(--maroon); color: white; padding: 5px 15px; border-radius: 50px; font-size: 11px;">Admin</span>
                <span style="font-weight: bold; font-size: 13px;">Alex Reyes</span>
            </div>
        </header>


        <div class="breadcrumb-spacer">PRISM / Inventory Management / Products</div>


        <div class="body-wrapper">
            <div class="content-box">
                <div class="content-header">
                    <div>
                        <h1>Products</h1>
                        <p>Manage Your Catalog and Stock Threshold</p>
                    </div>
                    <button class="btn-new" onclick="openModal()"><span>+</span> New Product</button>
                </div>


                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>PRODUCT NAME</th>
                                <th>CATEGORY</th>
                                <th>STATUS</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>BEV-001</td>
                                <td>Coca-Cola</td>
                                <td>Carbonated</td>
                                <td class="status-active">Active</td>
                                <td>
                                    <img src="../assets/edit_icon.png" class="action-img">
                                    <img src="../assets/delete_icon.png" class="action-img">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>


<div class="modal-overlay" id="newProductModal">
    <div class="modal-content">
        <button type="button" class="close-btn" onclick="closeModal()">&times;</button>
        <h2>New Product</h2>
        <p>Add new SKU to your catalog.</p>


        <form>
            <div class="details-section">
                <span class="details-label">Details</span>
               
                <div class="form-grid">
                    <div class="form-group">
                        <label>SKU</label>
                        <input type="text">
                        <span class="input-hint">3-20 alphanumeric characters. Immutable once set.</span>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select><option value=""></option><option>Carbonated</option></select>
                    </div>


                    <div class="form-group full-width">
                        <label>Name</label>
                        <input type="text">
                    </div>


                    <div class="form-group full-width">
                        <label>Description</label>
                        <textarea></textarea>
                    </div>


                    <div class="form-group">
                        <label>Unit Price</label>
                        <input type="text" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>Unit Cost</label>
                        <input type="text" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>Reorder Threshold</label>
                        <input type="text">
                    </div>
                    <div class="form-group">
                        <label>Supplier Name</label>
                        <input type="text">
                    </div>
                </div>
            </div>


            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-save">Create Product</button>
            </div>
        </form>
    </div>
</div>


<script>
    const modal = document.getElementById('newProductModal');
    function openModal() { modal.style.display = 'flex'; }
    function closeModal() { modal.style.display = 'none'; }
    window.onclick = function(e) { if (e.target == modal) closeModal(); }
</script>


</body>
</html>

