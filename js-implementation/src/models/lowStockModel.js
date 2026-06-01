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
     * Fetches products where current stock <= reorder threshold with server-side pagination boundaries.
     * @param {string|number} productId - 'all' or the specific product ID
     * @param {number|null} limit - Number of records to fetch (null fetches all)
     * @param {number} offset - Record skip starting window point
     */
    getLowStockProducts: async (productId = 'all', limit = 10, offset = 0) => {
        let baseQuery = `
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN stock_movements sm ON p.id = sm.product_id
            WHERE p.is_active = 1
        `;
        const queryParams = [];

        // Filter by specific product ID
        if (productId !== 'all') {
            baseQuery += ` AND p.id = ?`;
            queryParams.push(productId);
        }

        baseQuery += `
            GROUP BY 
                p.id, p.sku, p.name, c.name, p.reorder_threshold, p.supplier_name
            HAVING COALESCE(SUM(
                CASE 
                    WHEN sm.movement_type = 'out' AND sm.quantity > 0 THEN -sm.quantity
                    ELSE sm.quantity
                END
            ), 0) <= p.reorder_threshold
        `;

        // 1. Calculate the total count using a derived table subquery matching HAVING criteria
        const countQuery = `
            SELECT COUNT(*) AS total FROM (
                SELECT p.id ${baseQuery}
            ) AS total_count
        `;
        const [countRows] = await db.execute(countQuery, queryParams);
        const total = countRows[0].total;

        // 2. Fetch the specific paginated slice of data rows
        let dataQuery = `
            SELECT 
                p.id, p.sku, p.name, c.name AS category_name, p.reorder_threshold, p.supplier_name,
                COALESCE(SUM(
                    CASE 
                        WHEN sm.movement_type = 'out' AND sm.quantity > 0 THEN -sm.quantity
                        ELSE sm.quantity
                    END
                ), 0) AS current_quantity
            ${baseQuery}
            ORDER BY current_quantity ASC, p.name ASC
        `;

        // Only append pagination restrictions if a limit constraint is passed explicitly
        if (limit !== null) {
            dataQuery += ` LIMIT ${parseInt(limit)} OFFSET ${parseInt(offset)}`;
        }

        const [rows] = await db.execute(dataQuery, queryParams);
        return { rows, total };
    }
};

module.exports = LowStockModel;