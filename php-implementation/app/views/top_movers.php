<!DOCTYPE html>
<html lang = "en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>PRISM</title>
   <link rel="stylesheet" href="../../public/css/top_movers.css">
   <link rel="stylesheet" href="../../public/css/sidebar_header.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
</head>
<body>
   <?php include __DIR__ . '/sidebar_header.php'; ?>
   
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
               <span class="active">Low Stock</span>
               <span>Valuation</span>
               <span>Movement Ledger</span>
               <span>Top Movers</span>
            </div>
         </div>

         <div class="box2">
            <div class="box2_left">
            <h4>Top Movers</h4>
            <p class="box2_sub">Products with highest stock-out volume in range</p>
            </div>
            <div class="box2_right">
               <div class="dropdowns">
                  <select name="productDropdown" id="product">
                     <option value="allProducts">All Products</option>
                     <option value="product1">Category 1</option>
                     <option value="product2">Category 2</option>
                     <option value="product3">Category 3</option>
                  </select>
                  <select name="unitsDropdown" id="unit">
                     <option value="unitsOut">Units Out</option>
                     <option value="units1">1</option>
                     <option value="units2">2</option>
                     <option value="units3">3</option>
                  </select>
                  <button class="excel">CSV</button>
               </div>
            </div>
         </div>

         <div class="box3">
            <table>
               <tr class="header-box">
                  <th>SKU</th>
                  <th>Product Name</th>
                  <th>Category</th>
                  <th>Units Out</th>
               </tr>
               <tr>
                  <td>BEV-006</td>
                  <td>Pocari Sweat</td>
                  <td>Bottled Water</td>
                  <td>160</td>
               </tr>
            </table>
         </div>
      </div>
  </div>
 </body>
</html>