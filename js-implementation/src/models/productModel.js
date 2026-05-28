const db = require('../../config/db');

const ProductModel = {
    /**
     * Aggregates dynamic metrics for the system landing dashboard.
     * Modified to use 'movement_type' and match lowercase ENUM states ('in', 'out', 'adjustment').
     */
    getDashboardStats: async () => {
        const query = `
            SELECT 
                COUNT(DISTINCT CASE WHEN p.is_active = 1 THEN p.id END) as activeProducts,
                COUNT(DISTINCT CASE WHEN p.is_active = 0 THEN p.id END) as inactiveProducts,
                COALESCE(SUM(CASE 
                    WHEN m.movement_type = 'in' THEN m.quantity 
                    WHEN m.movement_type IN ('out', 'adjustment') THEN -m.quantity 
                    ELSE 0 
                END), 0) as totalUnits,
                COALESCE(SUM((CASE 
                    WHEN m.movement_type = 'in' THEN m.quantity 
                    WHEN m.movement_type IN ('out', 'adjustment') THEN -m.quantity 
                    ELSE 0 
                END) * p.unit_cost), 0) as inventoryValue
            FROM products p 
            LEFT JOIN stock_movements m ON p.id = m.product_id
        `;
        const [rows] = await db.execute(query);
        return rows[0]; 
    },

    /**
     * Identifies items falling behind safe inventory thresholds by scanning running transactional totals.
     * Modified to use 'movement_type' and match lowercase ENUM states ('in', 'out', 'adjustment').
     */
    getLowStockProducts: async () => {
        const query = `
            SELECT p.name, COALESCE(SUM(CASE 
                WHEN m.movement_type = 'in' THEN m.quantity 
                WHEN m.movement_type IN ('out', 'adjustment') THEN -m.quantity 
                ELSE 0 
            END), 0) as current_quantity
            FROM products p 
            LEFT JOIN stock_movements m ON p.id = m.product_id 
            WHERE p.is_active = 1
            GROUP BY p.id, p.name, p.reorder_threshold 
            HAVING current_quantity <= p.reorder_threshold 
            ORDER BY current_quantity ASC 
            LIMIT 5
        `;
        const [rows] = await db.execute(query);
        return rows; 
    },

    getAllProducts: async () => {
        const query = `
            SELECT id, sku, name, description, category_id, unit_price, unit_cost, reorder_threshold, supplier_name, is_active 
            FROM products 
            ORDER BY id DESC
        `;
        const [rows] = await db.execute(query);
        return rows;
    },

    searchProducts: async (term) => {
        const query = `
            SELECT id, sku, name, description, category_id, unit_price, unit_cost, reorder_threshold, supplier_name, is_active 
            FROM products 
            WHERE name LIKE ? OR sku LIKE ?
            ORDER BY id DESC
        `;
        const formattedTerm = `%${term}%`;
        const [rows] = await db.execute(query, [formattedTerm, formattedTerm]);
        return rows;
    },

    createProduct: async (sku, name, description, category_id, unit_cost, unit_price, reorder_threshold, supplier_name) => {
        const query = `
            INSERT INTO products (sku, name, description, category_id, unit_cost, unit_price, reorder_threshold, supplier_name, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
        `;
        const [result] = await db.execute(query, [sku, name, description, category_id, unit_cost, unit_price, reorder_threshold, supplier_name]);
        return result;
    },

    getAllActiveProducts: async () => {
        const query = `SELECT id, name, sku FROM products WHERE is_active = 1 ORDER BY name ASC`;
        const [rows] = await db.execute(query);
        return rows;
    }
};

module.exports = ProductModel;