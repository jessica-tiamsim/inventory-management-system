<?php
// app/controllers/ReportsController.php

class ReportsController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function index() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: " . BASE_URL . "/login");
            exit();
        }

        // 1. Handle Filters
        $filter_category = $_GET['category'] ?? 'all';
        
        $conditions = ["p.is_active = 1", "p.current_qty <= p.reorder_threshold"];
        $params = [];

        if ($filter_category !== 'all') {
            $conditions[] = "p.category_id = :cat_id";
            $params['cat_id'] = $filter_category;
        }

        $where_clause = implode(" AND ", $conditions);

        try {
            // 2. Fetch Low Stock Data
            $sql = "
                SELECT p.sku, p.name, c.name as category_name, p.current_qty, p.reorder_threshold, p.supplier_name 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE $where_clause
                ORDER BY p.current_qty ASC
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $low_stock_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 3. Fetch Categories for the Dropdown
            $categories = $this->pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

            // 4. Handle CSV Export Request
            if (isset($_GET['export']) && $_GET['export'] === 'csv') {
                $this->exportCSV($low_stock_items);
                exit(); // Stop loading the HTML view, just download the file
            }

        } catch (PDOException $e) {
            error_log("Reports Error: " . $e->getMessage());
            $low_stock_items = [];
            $categories = [];
        }

        // 5. Load the View
        require_once __DIR__ . '/../views/low_stock.php';
    }

    /**
     * Helper Method: Generate and download a CSV file
     */
    private function exportCSV($data) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=PRISM_LowStockReport_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        
        // Write CSV Column Headers
        fputcsv($output, ['SKU', 'Product Name', 'Category', 'Current Qty', 'Reorder Threshold', 'Supplier Name']);
        
        // Write Data Rows
        foreach ($data as $row) {
            fputcsv($output, [
                $row['sku'],
                $row['name'],
                $row['category_name'] ?? 'Uncategorized',
                $row['current_qty'],
                $row['reorder_threshold'],
                $row['supplier_name']
            ]);
        }
        fclose($output);
    }
    public function valuation() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: " . BASE_URL . "/login");
            exit();
        }

        // Dropdown sorting logic (matches the "Value" dropdown in your design)
        $sort = $_GET['sort'] ?? 'value';
        $order_by = $sort === 'category' ? 'c.name ASC' : 'total_value DESC';

        try {
            // Group by category and sum the total value of items inside it
            $sql = "
                SELECT c.name as category_name, SUM(p.current_qty * p.unit_cost) as total_value 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.is_active = 1
                GROUP BY c.id
                ORDER BY $order_by
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $valuation_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calculate the grand total
            $grand_total = array_sum(array_column($valuation_items, 'total_value'));

            // Optional: Handle CSV Export if clicked
            if (isset($_GET['export']) && $_GET['export'] === 'csv') {
                // You can add a CSV helper here later if needed!
                exit();
            }

        } catch (PDOException $e) {
            error_log("Valuation Report Error: " . $e->getMessage());
            $valuation_items = [];
            $grand_total = 0;
        }

        // Load the View
        require_once __DIR__ . '/../views/valuation.php';
    }
    
    public function movementLedger() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: " . BASE_URL . "/login");
            exit();
        }

        try {
            // Fetch the comprehensive audit trail (Latest 100 movements)
            $sql = "
                SELECT m.created_at, p.sku, p.name as product_name, m.type, m.quantity, u.username, m.notes 
                FROM stock_movements m
                JOIN products p ON m.product_id = p.id
                LEFT JOIN users u ON m.user_id = u.id
                ORDER BY m.created_at DESC
                LIMIT 100
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $ledger_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Movement Ledger Error: " . $e->getMessage());
            $ledger_items = [];
        }

        // Load the Ledger View
        require_once __DIR__ . '/../views/movement_ledger.php';
    }
    public function topMovers() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: " . BASE_URL . "/login");
            exit();
        }

        try {
            // Fetch items with the highest quantity of 'OUT' movements
            $sql = "
                SELECT p.sku, p.name as product_name, c.name as category_name, SUM(m.quantity) as total_moved
                FROM stock_movements m
                JOIN products p ON m.product_id = p.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE m.type = 'OUT'
                GROUP BY p.id
                ORDER BY total_moved DESC
                LIMIT 10
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $top_movers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Top Movers Error: " . $e->getMessage());
            $top_movers = [];
        }

        // Load the Top Movers View
        require_once __DIR__ . '/../views/top_movers.php';
    }
}