const db = require('../../config/db'); 

const ValuationModel = {
    /**
     * Calculates total inventory value aggregated by category
     * Value = Sum of (product unit_cost * mathematically corrected current quantity)
     */
    getInventoryValuation: async (sortBy = 'value') => {
        let query = `
            SELECT 
                c.name AS category_name,
                ROUND(SUM(p.unit_cost * COALESCE(sm_summary.current_quantity, 0)), 2) AS total_value
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            
            -- LEVEL 1: Generate an accurate warehouse inventory count summary matrix
            LEFT JOIN (
                SELECT 
                    product_id,
                    SUM(
                        CASE 
                            WHEN movement_type = 'in' THEN quantity
                            WHEN movement_type = 'out' THEN -ABS(quantity)
                            WHEN movement_type = 'adjustment' THEN quantity -- Handles positive/negative delta signs naturally
                            ELSE 0
                        END
                    ) AS current_quantity
                FROM stock_movements
                GROUP BY product_id
            ) sm_summary ON p.id = sm_summary.product_id
            
            -- Filter out deleted or deactivated product profiles
            WHERE p.is_active = 1
            GROUP BY c.id, c.name
        `;

        // LEVEL 3: Apply structural user sorting criteria safely
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