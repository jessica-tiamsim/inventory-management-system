<?php
// app/controllers/ProductsController.php

class ProductsController {
    private $pdo;

    // The router injects our database connection here automatically
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * 1. DISPLAY THE CATALOG VIEW (With Search & Filters)
     * Triggered when visiting: /products
     */
    public function index() {
        // Guard Check: Protect the route from logged-out users
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: " . BASE_URL . "/login?error=unauthorized");
            exit();
        }

        // 1. Capture all three filters from the URL (from our GET form)
        $search = trim($_GET['search'] ?? '');
        $category_filter = $_GET['category_filter'] ?? '';
        $status_filter = $_GET['status_filter'] ?? '2'; // '2' is our custom code for "All Status"
        $limit = 10;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        // 2. Build dynamic database conditions
        $conditions = [];
        $params = [];

        if ($search !== '') {
            // THE FIX: Using unique placeholders so PDO doesn't crash!
            $conditions[] = "(p.name LIKE :search_name OR p.sku LIKE :search_sku)";
            $params['search_name'] = '%' . $search . '%';
            $params['search_sku']  = '%' . $search . '%';
        }
        if ($category_filter !== '') {
            $conditions[] = "p.category_id = :category";
            $params['category'] = $category_filter;
        }
        if ($status_filter !== '2') { // Only filter status if it's NOT '2' (All)
            $conditions[] = "p.is_active = :status";
            $params['status'] = $status_filter;
        }

        // 3. Assemble the WHERE clause dynamically
        $where_clause = count($conditions) > 0 ? "WHERE " . implode(" AND ", $conditions) : "";

        try {
            // 4. Run the combined query to fetch products
            $count_sql = "
                SELECT COUNT(*)
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                $where_clause
            ";

            $count_stmt = $this->pdo->prepare($count_sql);
            $count_stmt->execute($params);
            $total_rows = (int)$count_stmt->fetchColumn();
            $total_pages = max(1, (int)ceil($total_rows / $limit));

            $catalog_sql = "
                SELECT p.id, p.sku, p.name, p.description, p.unit_price, p.unit_cost, p.reorder_threshold, p.is_active, c.name as category_name 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                $where_clause
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset
            ";
            
            $stmt = $this->pdo->prepare($catalog_sql);
            foreach ($params as $key => $val) {
                $stmt->bindValue(":$key", $val);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch categories to populate the dropdown filters and add/edit modals
            $categories = $this->pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("PRISM Catalog Controller Error: " . $e->getMessage());
            $products = [];
            $categories = [];
            $page = 1;
            $total_pages = 1;
        }

        // Load the view and pass the variables to the HTML
        require_once __DIR__ . '/../views/products.php';
    }

    /**
     * 2. PROCESS FORM SUBMISSION (ADD PRODUCT)
     * Triggered when the Add Product modal submits a POST request to: /products/add
     */
    public function add() {
        // Guard Check
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: " . BASE_URL . "/login?error=unauthorized");
            exit();
        }

        // Only process if the form was actually submitted via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Collect and sanitize our form input fields
            $sku = trim($_POST['sku'] ?? '');
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
            $unit_cost = (float)($_POST['unit_cost'] ?? 0.0);
            $unit_price = (float)($_POST['unit_price'] ?? 0.0);
            $reorder_threshold = (int)($_POST['reorder_threshold'] ?? 10);
            $supplier_name = trim($_POST['supplier_name'] ?? '');

            try {
                // Prepared statement protects against SQL Injection
                $sql = "INSERT INTO products (sku, name, description, category_id, unit_price, unit_cost, reorder_threshold, supplier_name, is_active) 
                        VALUES (:sku, :name, :description, :category_id, :unit_price, :unit_cost, :reorder_threshold, :supplier_name, 1)";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    'sku'               => $sku,
                    'name'              => $name,
                    'description'       => $description,
                    'category_id'       => $category_id,
                    'unit_price'        => $unit_price,
                    'unit_cost'         => $unit_cost,
                    'reorder_threshold' => $reorder_threshold,
                    'supplier_name'     => $supplier_name
                ]);

                // Success! Redirect safely using absolute URL
                header("Location: " . BASE_URL . "/products?success=product_added");
                exit();

            } catch (PDOException $e) {
                error_log("Failed to insert product: " . $e->getMessage());
                header("Location: " . BASE_URL . "/products?error=save_failed");
                exit();
            }
        }
    }

    /**
     * PROCESS PRODUCT EDIT
     */
    public function edit() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: " . BASE_URL . "/login?error=unauthorized");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $sql = "UPDATE products SET 
                        name = :name, description = :desc, category_id = :cat, 
                        unit_cost = :cost, unit_price = :price, 
                        reorder_threshold = :thresh, supplier_name = :supp,
                        updated_at = NOW()
                        WHERE id = :id";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    'name' => $_POST['name'],
                    'desc' => $_POST['description'],
                    'cat' => $_POST['category_id'],
                    'cost' => $_POST['unit_cost'],
                    'price' => $_POST['unit_price'],
                    'thresh' => $_POST['reorder_threshold'],
                    'supp' => $_POST['supplier_name'],
                    'id' => $_POST['id']
                ]);
                
                header("Location: " . BASE_URL . "/products?success=updated");
                exit();
            } catch (PDOException $e) {
                error_log("Edit Error: " . $e->getMessage());
                header("Location: " . BASE_URL . "/products?error=update_failed");
                exit();
            }
        }
    }

    /**
     * PROCESS PRODUCT INACTIVATION (SOFT DELETE)
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // We do NOT delete the row, we just set is_active = 0 so historical ledger data doesn't break!
                $stmt = $this->pdo->prepare("UPDATE products SET is_active = 0 WHERE id = :id");
                $stmt->execute(['id' => $_POST['id']]);
                
                header("Location: " . BASE_URL . "/products?success=inactivated");
                exit();
            } catch (PDOException $e) {
                error_log("Inactivate Error: " . $e->getMessage());
                header("Location: " . BASE_URL . "/products?error=inactivate_failed");
                exit();
            }
        }
    }
}