// src/models/stockMovementModel.js
const db = require('../../config/db');

const StockMovementModel = {
    getRecentActivity: async () => {
        const query = `
            SELECT 
                m.movement_type as type, 
                m.quantity, 
                p.name as productName,
                DATE_FORMAT(m.created_at, '%b %d, %h:%i %p') as timestamp
            FROM stock_movements m
            JOIN products p ON m.product_id = p.id
            ORDER BY m.created_at DESC
            LIMIT 5
        `;
        const [rows] = await db.execute(query);
        return rows; 
    }
};

module.exports = StockMovementModel;