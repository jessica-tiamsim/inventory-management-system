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
            // Fetch Products for the dropdowns (Modal & Filters)
            // Note: matching 'is_active = 1' instead of status='active' per your DB setup
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
                $conditions[] = "m.type = :type";
                $params['type'] = $filter_type;
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
            // Assumes stock_movements has user_id, product_id, type, quantity, notes
            $sql = "
                SELECT m.created_at, p.name AS product_name, m.type, m.quantity, u.username, m.notes 
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
            $type = $_POST['type']; // IN, OUT, ADJUSTMENT
            $quantity = (int)$_POST['quantity'];
            $notes = trim($_POST['notes'] ?? '');
            
            // Fallback user ID in case session data isn't fully structured yet
            $user_id = $_SESSION['user_id'] ?? 1; 

            try {
                $this->pdo->beginTransaction();

                // 1. Log to ledger
                $stmt = $this->pdo->prepare("INSERT INTO stock_movements (product_id, user_id, type, quantity, notes, created_at) VALUES (:pid, :uid, :type, :qty, :notes, NOW())");
                $stmt->execute([
                    'pid' => $product_id,
                    'uid' => $user_id,
                    'type' => $type,
                    'qty' => $quantity,
                    'notes' => $notes
                ]);

                // 2. Update Master Inventory Math
                $math_operator = ($type === 'IN' || $type === 'ADJUSTMENT' && $quantity > 0) ? '+' : '-';
                $update_qty = abs($quantity); // Ensure we don't accidentally subtract a negative

                $update_stmt = $this->pdo->prepare("UPDATE products SET current_qty = current_qty {$math_operator} :qty WHERE id = :pid");
                $update_stmt->execute(['qty' => $update_qty, 'pid' => $product_id]);

                $this->pdo->commit();
                header("Location: " . BASE_URL . "/stock-movement?success=recorded");
                exit();

            } catch (PDOException $e) {
                $this->pdo->rollBack();
                error_log("Transaction Failed: " . $e->getMessage());
                header("Location: " . BASE_URL . "/stock-movement?error=failed");
                exit();
            }
        }
    }
}