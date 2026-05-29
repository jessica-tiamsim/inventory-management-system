const db = require('../../config/db'); // Adjust path if needed

const ValuationModel = {
    /**
     * Calculates total inventory value aggregated by category
     * Value = Sum of (product unit_cost * calculated current quantity)
     */
    getInventoryValuation: async (sortBy = 'value') => {
        let query = `
            SELECT 
                c.name AS category_name,
                SUM(
                    p.unit_cost * COALESCE((
                        SELECT SUM(
                            CASE 
                                WHEN sm.movement_type = 'out' AND sm.quantity > 0 THEN -sm.quantity
                                ELSE sm.quantity
                            END
                        )
                        FROM stock_movements sm
                        WHERE sm.product_id = p.id
                    ), 0)
                ) AS total_value
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1
            GROUP BY c.id, c.name
        `;

        // Apply sorting criteria based on what your EJS dropdown expects
        if (sortBy === 'category') {
            query += ` ORDER BY c.name ASC`;
        } else {
            query += ` ORDER BY total_value DESC`; // Default: Highest value first
        }

        const [rows] = await db.execute(query);
        return rows;
    }
};

module.exports = ValuationModel;