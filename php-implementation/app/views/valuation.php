<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PRISM</title>
  <link rel="stylesheet" href="../../public/css/valuation.css">
    <link rel="stylesheet" href="../../public/css/sidebar_header.css">
    <link href='
</head>
<body>
  <?php require __DIR__ . "/sidebar_header.php"; ?>
    <!-- CONTENT -->
    <div class="content">

      <!-- PAGE HEADER -->
      <section class="page-header">

        <h2>Reports</h2>
        <p>Insights across your inventory operations.</p>

      </section>

      <!-- FILTERS -->
      <section class="report-filters">

        <div class="date-filters">

          <div class="filter-group">
            <label for="fromDate">From:</label>
            <input type="date" id="fromDate">
          </div>

          <div class="filter-group">
            <label for="toDate">To:</label>
            <input type="date" id="toDate">
          </div>

        </div>

        <div class="report-tabs">

          <button class="tab-btn">
            Low Stock
          </button>

          <button class="tab-btn active">
            Valuation
          </button>

          <button class="tab-btn">
            Movement Ledgers
          </button>

          <button class="tab-btn">
            Top Movers
          </button>

        </div>

      </section>

      <!-- REPORT CARD -->
      <section class="report-card">

        <div class="report-card-header">

          <div class="report-info">
            <h3>Inventory Valuation</h3>
            <p>Sum of (unit cost × current quantity) per category</p>
          </div>

          <div class="report-actions">

            <select id="sortBy">

              <option value="value">
                Value
              </option>

              <option value="category">
                Category
              </option>

            </select>

            <button class="csv-btn">
              CSV
            </button>

          </div>

        </div>

        <!-- TABLE -->
        <div class="report-table">

          <table>

            <thead>

              <tr>
                <th>Category</th>
                <th>Value</th>
              </tr>

            </thead>

            <tbody>

              <tr>
                <td>Dry Goods</td>
                <td>Php 602.00</td>
              </tr>

              <tr>
                <td>Beverages</td>
                <td>Php 408.00</td>
              </tr>

              <tr>
                <td>Snacks</td>
                <td>Php 289.00</td>
              </tr>

              <tr>
                <td>Cleaning</td>
                <td>Php 84.00</td>
              </tr>

            </tbody>

            <tfoot>

              <tr>
                <th>Total</th>
                <th>Php 1,403.00</th>
              </tr>

            </tfoot>

          </table>

        </div>

      </section>

</div>

</body>
</html>