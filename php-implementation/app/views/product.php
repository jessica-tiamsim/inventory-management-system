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
    <link rel="stylesheet" href="../../public/css/product.css">
    <link rel="stylesheet" href="../../public/css/sidebar_header.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
</head>
<body>
    <?php include __DIR__ . '/sidebar_header.php'; ?>
    <div class="content">
            <div class="content-box">
                <div class="content-header">
                    <div>
                        <h1>Products</h1>
                        <p>Manage Your Catalog and Stock Threshold</p>
                    </div>
                    <button class="btn-new"> + New Product</button>
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
</body>
</html>