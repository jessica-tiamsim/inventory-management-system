// src/models/productModel.js
const db = require('../../config/db');

const ProductModel = {
    // 1. Get the aggregate numbers for the top dashboard cards
    getDashboardStats: async () => {
        const query = `
            SELECT 
                COUNT(DISTINCT CASE WHEN p.is_active = 1 THEN p.id END) as activeProducts,
                COUNT(DISTINCT CASE WHEN p.is_active = 0 THEN p.id END) as inactiveProducts,
                COALESCE(SUM(
                    CASE 
                        WHEN m.movement_type = 'in' THEN m.quantity 
                        WHEN m.movement_type IN ('out', 'adjustment') THEN -m.quantity 
                        ELSE 0 
                    END
                ), 0) as totalUnits,
                COALESCE(SUM(
                    (CASE 
                        WHEN m.movement_type = 'in' THEN m.quantity 
                        WHEN m.movement_type IN ('out', 'adjustment') THEN -m.quantity 
                        ELSE 0 
                    END) * p.unit_cost
                ), 0) as inventoryValue
            FROM products p
            LEFT JOIN stock_movements m ON p.id = m.product_id
        `;
        const [rows] = await db.execute(query);
        return rows[0]; 
    },

    // 2. Get active products that are at or below their reorder threshold
    getLowStockProducts: async () => {
        const query = `
            SELECT 
                p.name, 
                COALESCE(SUM(
                    CASE 
                        WHEN m.movement_type = 'in' THEN m.quantity 
                        WHEN m.movement_type IN ('out', 'adjustment') THEN -m.quantity 
                        ELSE 0 
                    END
                ), 0) as current_quantity
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
    }
};

module.exports = ProductModel;