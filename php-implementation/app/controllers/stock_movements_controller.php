<?php
// app/controllers/StockMovementsController.php

class StockMovementsController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * 1. DISPLAY LEDGER WITH FILTERS AND PAGINATION
     */
    public function index() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: " . BASE_URL . "/login");
            exit();
        }

        // Initialize Filter & Pagination Variables
        $limit = 25;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;
        
        $filter_sku = $_GET['sku'] ?? 'all';
        $filter_type = $_GET['type'] ?? 'all';

        try {
            // Fetch Products for the dropdowns
            $products_stmt = $this->pdo->query("SELECT id, sku, name FROM products WHERE is_active = 1 ORDER BY name ASC");
            $products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);

            // Build dynamic WHERE clause based on user filters
            $conditions = [];
            $params = [];

            if ($filter_sku !== 'all') {
                $conditions[] = "p.sku = :sku";
                $params['sku'] = $filter_sku;
            }
            if ($filter_type !== 'all') {
                // Map the HTML filter to the correct DB column and lowercase it
                $conditions[] = "m.movement_type = :type";
                $params['type'] = strtolower($filter_type);
            }

            $where_clause = count($conditions) > 0 ? "WHERE " . implode(" AND ", $conditions) : "";

            // Pagination Math: Get total rows
            $count_sql = "
                SELECT COUNT(*) FROM stock_movements m 
                JOIN products p ON m.product_id = p.id 
                $where_clause
            ";
            $count_stmt = $this->pdo->prepare($count_sql);
            $count_stmt->execute($params);
            $total_rows = $count_stmt->fetchColumn();
            $total_pages = ceil($total_rows / $limit);

            // Fetch the actual movement data
            // We use UPPER(m.movement_type) AS type and m.note AS notes so your HTML doesn't break!
            $sql = "
                SELECT m.created_at, p.name AS product_name, UPPER(m.movement_type) AS type, m.quantity, u.username, m.note AS notes 
                FROM stock_movements m
                JOIN products p ON m.product_id = p.id
                LEFT JOIN users u ON m.user_id = u.id
                $where_clause
                ORDER BY m.created_at DESC 
                LIMIT :limit OFFSET :offset
            ";

            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $val) {
                $stmt->bindValue(":$key", $val);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $movements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Stock Movement Load Error: " . $e->getMessage());
            $movements = [];
            $products = [];
            $total_pages = 1;
        }

        // Pass variables to the View
        require_once __DIR__ . '/../views/stock_movements.php';
    }

    /**
     * 2. PROCESS NEW MOVEMENT (TRANSACTION)
     */
    public function add() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: " . BASE_URL . "/login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = (int)$_POST['product_id'];
            // Convert uppercase HTML type to lowercase ENUM for database
            $type = strtolower($_POST['type']); 
            $quantity = (int)$_POST['quantity'];
            $notes = trim($_POST['notes'] ?? '');
            
            $user_id = $_SESSION['user_id'] ?? 1; 

            try {
                // Just log to the ledger! (We removed the broken update to `current_qty`)
                $stmt = $this->pdo->prepare("INSERT INTO stock_movements (product_id, user_id, movement_type, quantity, note) VALUES (:pid, :uid, :type, :qty, :notes)");
                $stmt->execute([
                    'pid' => $product_id,
                    'uid' => $user_id,
                    'type' => $type,
                    'qty' => $quantity,
                    'notes' => $notes
                ]);

                header("Location: " . BASE_URL . "/stock-movement?success=recorded");
                exit();

            } catch (PDOException $e) {
                error_log("Transaction Failed: " . $e->getMessage());
                header("Location: " . BASE_URL . "/stock-movement?error=failed");
                exit();
            }
        }
    }
}