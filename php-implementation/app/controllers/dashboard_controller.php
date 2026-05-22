<?php
// app/controllers/DashboardController.php

class DashboardController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function index() {
        // Fix: Made redirect relative ("login") to prevent XAMPP 404 path breaks
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: login?error=unauthorized");
            exit();
        }

        $user_display_name = $_SESSION['username'] ?? 'User';

        // 1. Simple Counts (Updated: product_is_active -> is_active)
        $active_count = $this->pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn();
        $inactive_count = $this->pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 0")->fetchColumn();

        // 2. DRY PRINCIPLE: Define the reusable stock calculation subquery ONCE
        // (Uses stock_movements columns tracking product_id)
        $current_stock_subquery = "
            (SELECT product_id, 
                    SUM(CASE WHEN movement_type = 'in' THEN quantity WHEN movement_type = 'out' THEN -quantity ELSE quantity END) as qty
             FROM stock_movements
             GROUP BY product_id)
        ";

        // 3. Total Units
        $total_units = $this->pdo->query("SELECT SUM(qty) FROM $current_stock_subquery AS current_stock")->fetchColumn() ?: 0;

        // 4. Inventory Value (Updated: p.product_id -> p.id, product_is_active -> is_active)
        $inventory_value = $this->pdo->query("
            SELECT SUM(p.unit_cost * stock.qty) FROM products p
            JOIN $current_stock_subquery AS stock ON p.id = stock.product_id
            WHERE p.is_active = 1
        ")->fetchColumn() ?: 0;

        // 5. Low Stock Count (Updated: p.product_id -> p.id, product_is_active -> is_active)
        $low_stock_count = $this->pdo->query("
            SELECT COUNT(*) FROM products p
            LEFT JOIN $current_stock_subquery AS stock ON p.id = stock.product_id
            WHERE p.is_active = 1 AND COALESCE(stock.qty, 0) <= p.reorder_threshold
        ")->fetchColumn();

        // 6. Low Stock Items List (Updated: product_name -> name, p.product_id -> p.id, product_is_active -> is_active)
        $low_items = $this->pdo->query("
            SELECT p.sku, p.name, p.reorder_threshold, COALESCE(stock.qty, 0) as current_qty
            FROM products p
            LEFT JOIN $current_stock_subquery AS stock ON p.id = stock.product_id
            WHERE p.is_active = 1 AND COALESCE(stock.qty, 0) <= p.reorder_threshold
            ORDER BY current_qty ASC LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        // 7. Recent Activity List (Updated: p.product_name -> p.name, m.product_id = p.id)
        $activities = $this->pdo->query("
            SELECT m.movement_type, m.quantity, m.created_at, p.name 
            FROM stock_movements m
            JOIN products p ON m.product_id = p.id
            ORDER BY m.created_at DESC LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/dashboard.php';
    }
}