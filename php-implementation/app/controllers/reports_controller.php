<?php
// app/controllers/ReportsController.php

class ReportsController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * 1. DISPLAY THE LOW STOCK REPORT
     */
    public function low_stock() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: " . BASE_URL . "/login");
            exit();
        }

        $filter_category = $_GET['category'] ?? 'all';
        $export = $_GET['export'] ?? false;

        try {
            $categories_stmt = $this->pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
            $categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

            $conditions = [];
            $params = [];

            $conditions[] = "p.is_active = 1";
            $conditions[] = "COALESCE(stock.qty, 0) <= p.reorder_threshold";

            if ($filter_category !== 'all') {
                $conditions[] = "p.category_id = :cat_id";
                $params['cat_id'] = $filter_category;
            }

            $where_clause = "WHERE " . implode(" AND ", $conditions);

            $current_stock_subquery = "
                (SELECT product_id, 
                        SUM(CASE WHEN movement_type = 'in' THEN quantity WHEN movement_type = 'out' THEN -quantity ELSE quantity END) as qty
                 FROM stock_movements
                 GROUP BY product_id)
            ";

            $sql = "
                SELECT 
                    p.sku, 
                    p.name, 
                    c.name as category_name, 
                    COALESCE(stock.qty, 0) as current_qty, 
                    p.reorder_threshold, 
                    p.supplier_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN $current_stock_subquery AS stock ON p.id = stock.product_id
                $where_clause
                ORDER BY current_qty ASC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $low_stock_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($export === 'csv') {
                $this->exportCSV('low_stock_report.csv', $low_stock_items, ['SKU', 'Product Name', 'Category', 'Quantity', 'Reorder Threshold', 'Supplier Name']);
                exit();
            }

        } catch (PDOException $e) {
            error_log("Reports Controller Error: " . $e->getMessage());
            $categories = [];
            $low_stock_items = [];
        }

        require_once __DIR__ . '/../views/low_stock.php';
    }

    /**
     * 2. VALUATION REPORT
     */
    public function valuation() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: " . BASE_URL . "/login");
            exit();
        }

        $sort = $_GET['sort'] ?? 'value';
        $order_by = $sort === 'category' ? 'c.name ASC' : 'total_value DESC';

        try {
            $current_stock_subquery = "
                (SELECT product_id, 
                        SUM(CASE WHEN movement_type = 'in' THEN quantity WHEN movement_type = 'out' THEN -quantity ELSE quantity END) as qty
                 FROM stock_movements
                 GROUP BY product_id)
            ";

            $sql = "
                SELECT c.name as category_name, SUM(COALESCE(stock.qty, 0) * p.unit_cost) as total_value 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN $current_stock_subquery AS stock ON p.id = stock.product_id
                WHERE p.is_active = 1
                GROUP BY c.id
                ORDER BY $order_by
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $valuation_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $grand_total = array_sum(array_column($valuation_items, 'total_value'));

            if (isset($_GET['export']) && $_GET['export'] === 'csv') {
                $this->exportCSV('valuation_report.csv', $valuation_items, ['Category', 'Total Value (PHP)']);
                exit();
            }

        } catch (PDOException $e) {
            error_log("Valuation Report Error: " . $e->getMessage());
            $valuation_items = [];
            $grand_total = 0;
        }

        require_once __DIR__ . '/../views/valuation.php';
    }
    
    
    /**
     * 3. MOVEMENT LEDGER REPORT (Now with Date Filters!)
     */
    public function movementLedger() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: " . BASE_URL . "/login");
            exit();
        }

        // 1. Capture the dates from your HTML form
        $start_date = $_GET['start_date'] ?? '';
        $end_date = $_GET['end_date'] ?? '';

        try {
            $conditions = [];
            $params = [];

            // 2. Add SQL date filters if the user selected them
            if (!empty($start_date)) {
                $conditions[] = "DATE(m.created_at) >= :start_date";
                $params['start_date'] = $start_date;
            }
            if (!empty($end_date)) {
                $conditions[] = "DATE(m.created_at) <= :end_date";
                $params['end_date'] = $end_date;
            }

            $where_clause = count($conditions) > 0 ? "WHERE " . implode(" AND ", $conditions) : "";

            // 3. Fetch the filtered audit trail
            $sql = "
                SELECT m.created_at, p.sku, p.name as product_name, UPPER(m.movement_type) as type, m.quantity, u.username, m.note as notes 
                FROM stock_movements m
                JOIN products p ON m.product_id = p.id
                LEFT JOIN users u ON m.user_id = u.id
                $where_clause
                ORDER BY m.created_at DESC
                LIMIT 500
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $ledger_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Movement Ledger Error: " . $e->getMessage());
            $ledger_items = [];
        }

        require_once __DIR__ . '/../views/movement_ledger.php';
    }

    /**
     * 4. TOP MOVERS REPORT
     */
    public function topMovers() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: " . BASE_URL . "/login");
            exit();
        }

        try {
            $sql = "
                SELECT p.sku, p.name as product_name, c.name as category_name, SUM(m.quantity) as total_moved
                FROM stock_movements m
                JOIN products p ON m.product_id = p.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE m.movement_type = 'out'
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

        require_once __DIR__ . '/../views/top_movers.php';
    }

    /**
     * HELPER FUNCTION: CSV EXPORT
     */
    private function exportCSV($filename, $data, $headers) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        $output = fopen('php://output', 'w');
        fputcsv($output, $headers); 

        foreach ($data as $row) {
            fputcsv($output, array_values($row)); // Simply extracts the values in order
        }
        fclose($output);
    }
}