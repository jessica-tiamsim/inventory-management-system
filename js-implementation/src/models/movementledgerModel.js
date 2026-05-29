const db = require('../../config/db');

const movementLedgerModel = {
    /**
     * Extracts chronological inventory transaction logs inside optional date ranges
     */
    getMovementLedger: async (startDate = null, endDate = null) => {
        let query = `
            SELECT 
                sm.id,
                sm.created_at AS date_time,
                p.sku,
                p.name AS product_name,
                sm.movement_type,
                sm.quantity,
                u.username AS recorded_by,
                sm.note AS notes
            FROM stock_movements sm
            INNER JOIN products p ON sm.product_id = p.id
            LEFT JOIN users u ON sm.user_id = u.id
        `;
        
        const params = [];
        const conditions = [];

        if (startDate && startDate.trim() !== '') {
            conditions.push("sm.created_at >= ?");
            params.push(`${startDate} 00:00:00`);
        }

        if (endDate && endDate.trim() !== '') {
            conditions.push("sm.created_at <= ?");
            params.push(`${endDate} 23:59:59`);
        }

        if (conditions.length > 0) {
            query += " WHERE " + conditions.join(" AND ");
        }

        query += " ORDER BY sm.created_at DESC";

        const [rows] = await db.execute(query, params);
        return rows;
    }
};

module.exports = movementLedgerModel;