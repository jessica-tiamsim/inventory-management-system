// src/models/stockMovementModel.js
const db = require('../../config/db');

const StockMovementModel = {
    // Get the 5 most recent inventory changes
    getRecentActivity: async () => {
        const query = `
            SELECT 
                a.type, 
                a.quantity, 
                p.name as productName,
                DATE_FORMAT(a.timestamp, '%b %d, %h:%i %p') as timestamp
            FROM activity_logs a
            JOIN products p ON a.product_id = p.id
            ORDER BY a.timestamp DESC
            LIMIT 5
        `;
        const [rows] = await db.execute(query);
        return rows; // Returns an array of formatted activities
    }
};

module.exports = StockMovementModel;