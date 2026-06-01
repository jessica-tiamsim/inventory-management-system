const db = require('../../config/db');

const LowStockModel = {
    /**
     * Fetches low-stock products, optionally filtered by category.
     * @param {string|number} categoryId - 'all' or a specific category ID
     * @param {number|null}   limit      - rows per page (null = all)
     * @param {number}        offset     - pagination offset
     */
    getLowStockProducts: async (categoryId = 'all', limit = 10, offset = 0) => {
        const params = [];

        let baseWhere = `WHERE p.is_active = 1`;

        if (categoryId !== 'all' && categoryId !== '') {
            baseWhere += ` AND p.category_id = ?`;
            params.push(parseInt(categoryId, 10));
        }

        // Count query using a subquery so HAVING works correctly
        const countQuery = `
            SELECT COUNT(*) AS total FROM (
                SELECT p.id
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN stock_movements sm ON p.id = sm.product_id
                ${baseWhere}
                GROUP BY p.id, p.sku, p.name, c.name, p.reorder_threshold, p.supplier_name
                HAVING COALESCE(SUM(
                    CASE
                        WHEN sm.movement_type = 'in'  THEN  sm.quantity
                        WHEN sm.movement_type = 'out' THEN -sm.quantity
                        WHEN sm.movement_type = 'adjustment' THEN sm.quantity
                        ELSE 0
                    END
                ), 0) <= p.reorder_threshold
            ) AS sub
        `;
        const [countRows] = await db.execute(countQuery, params);
        const total = countRows[0].total;

        let dataQuery = `
            SELECT
                p.id, p.sku, p.name,
                c.name AS category_name,
                p.reorder_threshold,
                p.supplier_name,
                COALESCE(SUM(
                    CASE
                        WHEN sm.movement_type = 'in'  THEN  sm.quantity
                        WHEN sm.movement_type = 'out' THEN -sm.quantity
                        WHEN sm.movement_type = 'adjustment' THEN sm.quantity
                        ELSE 0
                    END
                ), 0) AS current_quantity
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN stock_movements sm ON p.id = sm.product_id
            ${baseWhere}
            GROUP BY p.id, p.sku, p.name, c.name, p.reorder_threshold, p.supplier_name
            HAVING current_quantity <= p.reorder_threshold
            ORDER BY current_quantity ASC, p.name ASC
        `;

        if (limit !== null) {
            dataQuery += ` LIMIT ${parseInt(limit)} OFFSET ${parseInt(offset)}`;
        }

        const [rows] = await db.execute(dataQuery, params);
        return { rows, total };
    }
};

module.exports = LowStockModel;
