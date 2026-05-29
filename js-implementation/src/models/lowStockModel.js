const db = require('../../config/db'); // Adjust path to your db pool if necessary

const LowStockModel = {
    /**
     * Fetches all categories to populate the filter dropdown
     */
    getCategories: async () => {
        const query = `SELECT id, name FROM categories ORDER BY name ASC`;
        const [rows] = await db.execute(query);
        return rows;
    },

    /**
     * Fetches products where current stock <= reorder threshold
     * Calculates current stock by summing stock movements.
     * @param {string|number} categoryId - 'all' or the specific category ID
     */
    getLowStockProducts: async (categoryId = 'all') => {
        let query = `
            SELECT 
                p.sku, 
                p.name, 
                c.name AS category_name, 
                p.reorder_threshold, 
                p.supplier_name,
                COALESCE(SUM(
                    CASE 
                        -- If 'out' is logged as a positive number, force it to be negative for the sum
                        WHEN sm.movement_type = 'out' AND sm.quantity > 0 THEN -sm.quantity
                        ELSE sm.quantity
                    END
                ), 0) AS current_quantity
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN stock_movements sm ON p.id = sm.product_id
            WHERE p.is_active = 1
        `;

        const queryParams = [];

        // Apply category filter if one is selected
        if (categoryId !== 'all') {
            query += ` AND p.category_id = ?`;
            queryParams.push(categoryId);
        }

        // 🟢 FIXED: Explicitly group by all non-aggregated columns to satisfy strict SQL modes
        query += `
            GROUP BY 
                p.id,
                p.sku,
                p.name,
                c.name,
                p.reorder_threshold,
                p.supplier_name
            HAVING current_quantity <= p.reorder_threshold
            ORDER BY current_quantity ASC, p.name ASC
        `;

        const [rows] = await db.execute(query, queryParams);
        return rows;
    }
};

module.exports = LowStockModel;