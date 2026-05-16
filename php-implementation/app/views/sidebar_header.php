<?php
// At the top of sidebar_header.php
if (!isset($current_page)) {
    $current_page = basename($_SERVER['PHP_SELF']); 
}
?>
   <aside>
      <div class= "logo">
         <div class="logo1">
            <img src= "../../../assets/logo.png" class="prism_image">
            <h1>PRISM</h1>
         </div>
         <div class="logo_user">
            <p><?= ($_SESSION['role'] === 'admin') ? 'Admin Portal' : 'Staff Portal' ?></p>
         </div>
      </div>
      
      <nav>
         <a href="dashboard.php" class="<?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
            <img src="image.png">
            <p>Dashboard</p>
         </a>
         <a href="product.php" class="<?= ($current_page == 'product.php') ? 'active' : '' ?>">
            <img src="image.png">
            <p>Products</p>
         </a>
         <a href="stock_movement.php" class="<?= ($current_page == 'stock_movement.php') ? 'active' : '' ?>">
            <img src="image.png">
            <p>Stock Movements</p>
         </a>
         <a href="low_stock.php" class="<?= ($current_page == 'low_stock.php') ? 'active' : '' ?>">
            <img src="image.png">
            <p>Reports</p>
         </a>
         <?php if ($_SESSION['role'] === 'admin'): ?>
         <a href="users.php" class="<?= ($current_page == 'users.php') ? 'active' : '' ?>">
            <img src="image.png">
            <p>Profile</p>
         </a>
         <?php endif; ?>
      </nav>

      <div class ="user-panel">
         <div class="user">
            <div class="profile">
               <img src="image.png" class="prism_image">
            </div>
            <div class="status">
               <p><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></p>
               <p id=admin><?= ucfirst(htmlspecialchars($_SESSION['role'] ?? 'Staff')) ?></p>
            </div>
         </div>
         <button href="logout.php">
            <img src="image.png" class="logout_image">
            <p id=logout>Logout</p>
         </button>
      </div>
   </aside>
   <div class="main">
   <header>
      <div class= "upper">
         <div class= "left">
            <!--<img src="C:\xampp\htdocs\inventory-management-system\php-implementation\image.png">-->
            <div class="shape"><img src="#"></div>
            <p>PRISM | Inventory Management</p>
         </div>
         <div class="right">
            <div class = "admin">
               <p><?= ucfirst(htmlspecialchars($_SESSION['role'] ?? 'Staff')) ?></p>
            </div>
            <div class = "alex">
               <p><?= htmlspecialchars($_SESSION['username'] ?? 'User Name') ?></p>
            </div> 
         </div>
      </div>
      <div class="lower">
         <p>PRISM / INVENTORY MANAGEMENT / <?= strtoupper(str_replace(['.php', '_'], ['', ' '], $current_page)) ?></p>
      </div>
   </header>


