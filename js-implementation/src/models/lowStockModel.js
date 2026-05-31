const db = require('../../config/db'); 

const LowStockModel = {
    /**
     * Fetches all active products to populate the filter dropdown
     */
    getProductsList: async () => {
        const query = `SELECT id, name, sku FROM products WHERE is_active = 1 ORDER BY name ASC`;
        const [rows] = await db.execute(query);
        return rows;
    },

    /**
     * Fetches products where current stock <= reorder threshold
     * @param {string|number} productId - 'all' or the specific product ID
     */
    getLowStockProducts: async (productId = 'all') => {
        let query = `
            SELECT 
                p.id,
                p.sku, 
                p.name, 
                c.name AS category_name, 
                p.reorder_threshold, 
                p.supplier_name,
                COALESCE(SUM(
                    CASE 
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

        // Filter by specific product ID
        if (productId !== 'all') {
            query += ` AND p.id = ?`;
            queryParams.push(productId);
        }

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