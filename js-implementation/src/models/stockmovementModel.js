const db = require('../../config/db');

const stockmovementModel = {
    /**
     * Traverses history logs matching filter row query states from the interface.
     * Maps perfectly to the official 'movement_type' and 'note' layout columns.
     * @param {string} sku - Product stock keeping unit indicator or 'all'
     * @param {string} type - 'IN', 'OUT', 'ADJUSTMENT', or 'all'
     */
    
    /**
     * Traverses history logs matching filter row query states with server-side pagination boundaries.
     */
    getMovementsByFilters: async (sku, type, limit = 10, offset = 0) => {
        let whereClause = ' WHERE 1=1';
        const params = [];

        if (sku && sku !== 'all') {
            whereClause += ` AND p.sku = ?`;
            params.push(sku);
        }
        if (type && type !== 'all') {
            whereClause += ` AND sm.movement_type = LOWER(?)`;
            params.push(type);
        }

        // 1. Get total records matching these filters
        const countQuery = `
            SELECT COUNT(*) AS total
            FROM stock_movements sm
            JOIN products p ON sm.product_id = p.id
            ${whereClause}
        `;
        const [countRows] = await db.execute(countQuery, params);
        const total = countRows[0].total;

        // 2. Get the specific subset of paginated data rows
        // Appending limit and offset values directly removes SQL placeholder driver casting issues
        const dataQuery = `
            SELECT sm.*, sm.movement_type AS type, sm.note, 
            DATE_FORMAT(sm.created_at, '%Y-%m-%d %h:%i %p') AS date, 
            p.name AS product_name, p.sku, u.username AS recorded_by
            FROM stock_movements sm
            JOIN products p ON sm.product_id = p.id
            JOIN users u ON sm.user_id = u.id
            ${whereClause}
            ORDER BY sm.id DESC
            LIMIT ${parseInt(limit)} OFFSET ${parseInt(offset)}
        `;
        
        const [rows] = await db.execute(dataQuery, params);
        return { rows, total };
    },

    /**
     * Records a new transaction entry into the system ledger database logs.
     * Uses LOWER(?) to convert front-end form inputs into database ENUM lowercase values.
     */
    createTransaction: async (productId, type, quantity, notes, userId) => {
        const connection = await db.getConnection();
        try {
            // Initiate atomic transactional engine boundary lock
            await connection.beginTransaction(); 

            // Insert structural ledger transaction log details using matching schema column tags
            const insertQuery = `
                INSERT INTO stock_movements (product_id, movement_type, quantity, note, user_id, created_at)
                VALUES (?, LOWER(?), ?, ?, ?, NOW())
            `;
            await connection.execute(insertQuery, [productId, type, quantity, notes || null, userId]);

            // Save adjustments to disk infrastructure concurrently
            await connection.commit();
        } catch (error) {
            // Revert changes back safely if any steps drop an error flag
            await connection.rollback();
            throw error;
        } finally {
            // Return connection pool resource thread to system engine storage
            connection.release();
        }
    },

    /**
     * Fetches the latest 5 updates across all lines for the main landing page activity log module.
     */
    getRecentActivity: async () => {
        const query = `
            SELECT sm.*, sm.movement_type AS type, sm.note, 
            DATE_FORMAT(sm.created_at, '%Y-%m-%d %h:%i %p') AS date, 
            p.name AS product_name, p.sku, u.username AS recorded_by
            FROM stock_movements sm
            JOIN products p ON sm.product_id = p.id
            JOIN users u ON sm.user_id = u.id
            ORDER BY sm.id DESC 
            LIMIT 5
        `;
        const [rows] = await db.execute(query);
        return rows;
    }
};

module.exports = stockmovementModel;