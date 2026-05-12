<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PRISM</title>
  <link rel="stylesheet" href="../../public/css/stock_movement.css">
  <link rel="stylesheet" href="../../public/css/sidebar_header.css">
  <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
</head>
<body>

<?php include __DIR__ . '/sidebar_header.php'; ?>

    <main class="content">
       <div class="box1">
            <div class="title-group">
                <span class="pageTitle">Stock Movements</span>
                <p class="pageSubtitle">
                    Immutable ledger of stock change.
                </p>
                <div class="dropdowns">
                    <select name="productDropDown" id="product">
                    <option value="allProducts">All Products</option>
                    <option value="product1">Category 1</option>
                    <option value="product2">Category 2</option>
                    <option value="Product3">Category 3</option>
                    </select>
                    <select name="typeDropDown" id="type">
                    <option value="allTypes">All Types</option>
                    <option value="product1">Type 1</option>
                    <option value="product2">Type 2</option>
                    <option value="Product3">Type 3</option>
                    </select>
                </div>
            </div>


            <button class="record">
                + Record Movement
            </button>


        </div>
        <div class="box2">
          The table goes here
        </div>
    </main>

  </div>
</div>
</body>
</html>