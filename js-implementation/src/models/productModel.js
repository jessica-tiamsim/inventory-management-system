// src/models/productModel.js
const db = require('../../config/db');

const ProductModel = {
    // 1. Get the aggregate numbers for the top dashboard cards
    getDashboardStats: async () => {
        // COALESCE ensures we return 0 instead of 'null' if the database is totally empty
        const query = `
            SELECT 
                COUNT(CASE WHEN status = 'active' THEN 1 END) as activeProducts,
                COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactiveProducts,
                COALESCE(SUM(current_quantity), 0) as totalUnits,
                COALESCE(SUM(current_quantity * unit_cost), 0) as inventoryValue
            FROM products
        `;
        const [rows] = await db.execute(query);
        return rows[0]; 
    },

    // 2. Get active products that are at or below their reorder threshold
    getLowStockProducts: async () => {
        const query = `
            SELECT name, current_quantity 
            FROM products 
            WHERE current_quantity <= reorder_threshold 
            AND status = 'active'
            ORDER BY current_quantity ASC 
            LIMIT 5
        `;
        const [rows] = await db.execute(query);
        return rows; // Returns an array of products
    }
};

module.exports = ProductModel;