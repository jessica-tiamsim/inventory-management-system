<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PRISM</title>
  <link rel="stylesheet" href="../../public/css/movement_ledger.css">
    <link rel="stylesheet" href="../../public/css/sidebar_header.css">
</head>
<body>
  <?php require __DIR__ . "/sidebar_header.php"; ?>

      <div class ="content">
         <div class="box1">
            <h1 class="box1_title">Reports</h1>
            <p class="box1_sub">Insight across your inventory operations</p>
            <div class="date">
               <label for="date">From: </label>
               <input type="date" id="date1">


               <label for="date">To: </label>
               <input type="date" id="date2">
            </div>
            <div class="choices">
               <span>Low Stock</span>
               <span>Valuation</span>
               <span class="active">Movement Ledger</span>
               <span>Top Movers</span>
            </div>
         </div>


         <div class="box2">
            <div class="box2_left">
            <h4>Movement Ledger</h4>
            <p class="box2_sub">Filterable List of Movements</p>
            </div>
            <div class="box2_right">
               <div class="dropdowns">
                  <select name="productDropdown" id="product">
                     <option value="allProducts">All Products</option>
                     <option value="product1">Category 1</option>
                     <option value="product2">Category 2</option>
                     <option value="product3">Category 3</option>
                  </select>
                  <select name="quantityDropdown" id="product">
                     <option value="allProducts">Quantity</option>
                     <option value="quantity1">1</option>
                     <option value="quantity2">2</option>
                     <option value="quantity3">3</option>
                  </select>
                  <button class="excel">CSV</button>
               </div>
            </div>
         </div>


         <div class="box3">
            <table>
               <tr class="header-box">
                  <th>Date</th>
                  <th>Product Name</th>
                  <th>Type</th>
                  <th>Quantity</th>
                  <th>Reorder Threshold</th>
               </tr>
               <tr>
                  <td>2023-10-01</td>
                  <td>Coca-Cola</td>
                  <td>Out</td>
                  <td>165</td>
                  <td>Shop Selling</td>
               </tr>
               <tr>
                  <td>2023-10-02</td>
                  <td>Coca-Cola</td>
                  <td>Out</td>
                  <td>165</td>
                  <td>Shop Selling</td>
               </tr>
               <tr>
                  <td>2023-10-03</td>
                  <td>Coca-Cola</td>
                  <td>Out</td>
                  <td>165</td>
                  <td>Shop Selling</td>
               </tr>
               <tr>
                  <td>2023-10-04</td>
                  <td>Coca-Cola</td>
                  <td>Out</td>
                  <td>165</td>
                  <td>Shop Selling</td>
               </tr>
               <tr>
                  <td>2023-10-04</td>
                  <td>Coca-Cola</td>
                  <td>Out</td>
                  <td>165</td>
                  <td>Shop Selling</td>
               </tr>
               <tr>
                  <td>2023-10-04</td>
                  <td>Coca-Cola</td>
                  <td>Out</td>
                  <td>165</td>
                  <td>Shop Selling</td>
               </tr>
               <tr>
                  <td>2023-10-04</td>
                  <td>Coca-Cola</td>
                  <td>Out</td>
                  <td>165</td>
                  <td>Shop Selling</td>
               </tr>
               <tr>
                  <td>2023-10-04</td>
                  <td>Coca-Cola</td>
                  <td>Out</td>
                  <td>165</td>
                  <td>Shop Selling</td>
               </tr>
            </table>
         </div>
      </div>
  </div>
 </body>
</html>