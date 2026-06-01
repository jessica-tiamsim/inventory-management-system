const db = require('../../config/db');

const topMoversModel = {
    /**
     * Aggregates and ranks products by total outbound stock volume
     */
    getTopMovers: async (startDate = null, endDate = null) => {
        try {
            let query = `
                SELECT 
                    p.sku,
                    p.name AS product_name,
                    c.name AS category_name,
                    SUM(sm.quantity) AS units_out
                FROM stock_movements sm
                INNER JOIN products p ON sm.product_id = p.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE sm.movement_type = 'out'
            `;
            
            const params = [];

            if (startDate && startDate.trim() !== '') {
                query += " AND sm.created_at >= ?";
                params.push(`${startDate} 00:00:00`);
            }
            if (endDate && endDate.trim() !== '') {
                query += " AND sm.created_at <= ?";
                params.push(`${endDate} 23:59:59`);
            }

            // Group transactions by product profile properties
            query += `
                GROUP BY p.id, p.sku, p.name, c.name
                ORDER BY units_out DESC, p.name ASC
            `;

            const [rows] = await db.execute(query, params);
            return rows;
        } catch (error) {
            console.error("Database query exception inside topMoversModel:", error);
            throw error;
        }
    }
};

module.exports = topMoversModel;