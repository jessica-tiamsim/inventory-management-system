const db = require('../../config/db'); 

const ValuationModel = {
    /**
     * Calculates total inventory value aggregated by category
     * Value = Sum of (product unit_cost * calculated current quantity)
     */
    getInventoryValuation: async (sortBy = 'value') => {
        let query = `
            SELECT 
                c.name AS category_name,
                ROUND(SUM(p.unit_cost * COALESCE(sm_summary.current_quantity, 0)), 2) AS total_value
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            -- Pre-calculate quantities per product to avoid correlated subquery flaws
            LEFT JOIN (
                SELECT 
                    product_id,
                    SUM(
                        CASE 
                            WHEN LOWER(movement_type) = 'out' THEN -ABS(quantity)
                            ELSE ABS(quantity)
                        END
                    ) AS current_quantity
                FROM stock_movements
                GROUP BY product_id
            ) sm_summary ON p.id = sm_summary.product_id
            WHERE p.is_active = 1
            GROUP BY c.id, c.name
        `;

        // Apply sorting criteria
        if (sortBy === 'category') {
            query += ` ORDER BY c.name ASC`;
        } else {
            query += ` ORDER BY total_value DESC`; 
        }

        const [rows] = await db.execute(query);
        return rows;
    }
};

module.exports = ValuationModel;