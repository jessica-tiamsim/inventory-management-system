const db = require('../../config/db');

const movementLedgerModel = {
    /**
     * Extracts chronological inventory transaction logs inside optional criteria layers
     * Extended to accept limit and offset parameters for database-driven pagination.
     */
    getMovementLedger: async (startDate = null, endDate = null, productId = 'all', movementType = 'all', limit = null, offset = null) => {
        try {
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

            // 1. Date Range Filters
            if (startDate && startDate.trim() !== '') {
                conditions.push("sm.created_at >= ?");
                params.push(`${startDate} 00:00:00`);
            }
            if (endDate && endDate.trim() !== '') {
                conditions.push("sm.created_at <= ?");
                params.push(`${endDate} 23:59:59`);
            }

            // 2. Product ID Filter
            if (productId && productId !== 'all' && productId.trim() !== '') {
                conditions.push("sm.product_id = ?");
                params.push(productId);
            }

            // 3. Movement Type Filter
            if (movementType && movementType !== 'all' && movementType.trim() !== '') {
                conditions.push("sm.movement_type = ?");
                params.push(movementType);
            }

            if (conditions.length > 0) {
                query += " WHERE " + conditions.join(" AND ");
            }

            query += " ORDER BY sm.created_at DESC";

            // Append Pagination limits only if passed
            if (limit !== null && offset !== null) {
                query += " LIMIT ? OFFSET ?";
                params.push(Number(limit), Number(offset));
            }

            const [rows] = await db.execute(query, params);
            return rows;

        } catch (error) {
            console.error("Database query exception inside movementLedgerModel.getMovementLedger:", error);
            throw error;
        }
    },

    /**
     * Obtains total row count match metrics for calculating pagination intervals
     */
    getMovementLedgerCount: async (startDate = null, endDate = null, productId = 'all', movementType = 'all') => {
        try {
            let query = `SELECT COUNT(*) as total FROM stock_movements sm`;
            const params = [];
            const conditions = [];

            if (startDate && startDate.trim() !== '') { conditions.push("sm.created_at >= ?"); params.push(`${startDate} 00:00:00`); }
            if (endDate && endDate.trim() !== '') { conditions.push("sm.created_at <= ?"); params.push(`${endDate} 23:59:59`); }
            if (productId && productId !== 'all' && productId.trim() !== '') { conditions.push("sm.product_id = ?"); params.push(productId); }
            if (movementType && movementType !== 'all' && movementType.trim() !== '') { conditions.push("sm.movement_type = ?"); params.push(movementType); }

            if (conditions.length > 0) query += " WHERE " + conditions.join(" AND ");

            const [rows] = await db.execute(query, params);
            return rows[0].total;
        } catch (error) {
            console.error("Error fetching ledger row totals:", error);
            throw error;
        }
    },

    getAllProductsList: async () => {
        try {
            const [rows] = await db.execute("SELECT id, name FROM products ORDER BY name ASC");
            return rows;
        } catch (error) {
            console.error("Database query exception inside movementLedgerModel.getAllProductsList:", error);
            throw error;
        }
    }
};

module.exports = movementLedgerModel;