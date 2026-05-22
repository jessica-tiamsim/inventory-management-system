<?php
// app/controllers/ProductsController.php

class ProductsController {
    private $pdo;

    // The router injects our database connection here automatically
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * 1. DISPLAY THE CATALOG VIEW
     * Triggered when visiting: /products
     */
    public function index() {
        // Guard Check: Protect the route from logged-out users
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            // FIXED: Added BASE_URL for absolute routing
            header("Location: " . BASE_URL . "/login?error=unauthorized");
            exit();
        }

        try {
            // Fetch complete catalog with category text matching Page 12 requirements
            $catalog_sql = "
                SELECT p.id, p.sku, p.name, p.description, p.unit_price, p.unit_cost, p.reorder_threshold, p.is_active, c.name as category_name 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                ORDER BY p.created_at DESC";
            $products = $this->pdo->query($catalog_sql)->fetchAll(PDO::FETCH_ASSOC);

            // Fetch categories to populate your modal & filters dropdowns dynamically
            $categories = $this->pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("PRISM Catalog Controller Error: " . $e->getMessage());
            $products = [];
            $categories = [];
        }

        // Send variables ($products, $categories) safely into the view
        require_once __DIR__ . '/../views/products.php';
    }

    /**
     * 2. PROCESS FORM SUBMISSION (ADD PRODUCT)
     * Triggered when the Add Product modal submits a POST request to: /products/add
     */
    public function add() {
        // Guard Check
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            // FIXED: Added BASE_URL for absolute routing
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

                // FIXED: Added BASE_URL to prevent the /products/products relative routing loop
                header("Location: " . BASE_URL . "/products?success=product_added");
                exit();

            } catch (PDOException $e) {
                // If it crashes (e.g., duplicate SKU entry), capture error log and bounce back safely
                error_log("Failed to insert product: " . $e->getMessage());
                // FIXED: Added BASE_URL for absolute routing
                header("Location: " . BASE_URL . "/products?error=save_failed");
                exit();
            }
        }
    }
    /**
     * 3. PROCESS DELETION
     * Triggered by /product/delete?id=123
     */
    public function delete() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: " . BASE_URL . "/login");
            exit();
        }

        $id = $_GET['id'] ?? null;

        if ($id) {
            try {
                // Delete the product from the database
                $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = :id");
                $stmt->execute(['id' => $id]);
                header("Location: " . BASE_URL . "/products?success=product_deleted");
                exit();
            } catch (PDOException $e) {
                error_log("Failed to delete product: " . $e->getMessage());
                header("Location: " . BASE_URL . "/products?error=delete_failed");
                exit();
            }
        }
        
        header("Location: " . BASE_URL . "/products");
        exit();
    }

    /**
     * 4. DISPLAY EDIT FORM & PROCESS UPDATES
     * Triggered by /product/edit?id=123
     */
    public function edit() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: " . BASE_URL . "/login");
            exit();
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: " . BASE_URL . "/products");
            exit();
        }

        // If the form was submitted (POST), update the database!
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sku = trim($_POST['sku']);
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
            $unit_cost = (float)$_POST['unit_cost'];
            $unit_price = (float)$_POST['unit_price'];
            $reorder_threshold = (int)$_POST['reorder_threshold'];
            $supplier_name = trim($_POST['supplier_name']);
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            try {
                $sql = "UPDATE products SET 
                        sku = :sku, name = :name, description = :description, 
                        category_id = :category_id, unit_price = :unit_price, 
                        unit_cost = :unit_cost, reorder_threshold = :reorder_threshold, 
                        supplier_name = :supplier_name, is_active = :is_active 
                        WHERE id = :id";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    'sku' => $sku, 'name' => $name, 'description' => $description,
                    'category_id' => $category_id, 'unit_price' => $unit_price,
                    'unit_cost' => $unit_cost, 'reorder_threshold' => $reorder_threshold,
                    'supplier_name' => $supplier_name, 'is_active' => $is_active, 'id' => $id
                ]);

                header("Location: " . BASE_URL . "/products?success=product_updated");
                exit();
            } catch (PDOException $e) {
                error_log("Failed to update product: " . $e->getMessage());
                $error = "Failed to update product.";
            }
        }

        // If not a POST request, fetch the product data to populate the HTML form
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            header("Location: " . BASE_URL . "/products?error=not_found");
            exit();
        }

        // Fetch categories for the dropdown
        $categories = $this->pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

        // Load the Edit View
        require_once __DIR__ . '/../views/edit_product.php';
    }
}