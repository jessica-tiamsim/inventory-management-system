const db = require('../../config/db');

const stockmovementModel = {
    /**
     * Traverses history logs matching filter row query states from the interface.
     * Maps perfectly to the official 'movement_type' and 'note' layout columns.
     * @param {string} sku - Product stock keeping unit indicator or 'all'
     * @param {string} type - 'IN', 'OUT', 'ADJUSTMENT', or 'all'
     */
    getMovementsByFilters: async (sku, type) => {
        let query = `
            SELECT sm.*, sm.movement_type AS type, sm.note, p.name AS product_name, p.sku, u.username AS recorded_by
            FROM stock_movements sm
            JOIN products p ON sm.product_id = p.id
            JOIN users u ON sm.user_id = u.id
            WHERE 1=1
        `;
        const params = [];

        if (sku && sku !== 'all') {
            query += ` AND p.sku = ?`;
            params.push(sku);
        }
        if (type && type !== 'all') {
            // Converts incoming uppercase filter values to lowercase matching the database ENUM
            query += ` AND sm.movement_type = LOWER(?)`;
            params.push(type);
        }

        query += ` ORDER BY sm.id DESC`;
        const [rows] = await db.execute(query, params);
        return rows;
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
            SELECT sm.*, sm.movement_type AS type, sm.note, p.name AS product_name, u.username AS recorded_by
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