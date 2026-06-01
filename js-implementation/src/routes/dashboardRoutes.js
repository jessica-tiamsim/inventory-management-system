// src/routes/dashboardRoutes.js
const express = require('express');
const router = express.Router();

const productModel = require('../models/productModel');
const stockMovementModel = require('../models/stockmovementModel');
const { verifySession } = require('../middlewares/authMiddleware');

// Maps to '/dashboard' via index.js
router.get('/', verifySession, async (req, res) => {
    try {
        const [rawStats, lowStockProducts, recentRows] = await Promise.all([
            productModel.getDashboardStats(),
            productModel.getLowStockProducts(),
            stockMovementModel.getRecentActivity()
        ]);

        // Normalise stats into the shape the view expects
        const stats = {
            activeProducts:   rawStats.activeProducts   || 0,
            inactiveProducts: rawStats.inactiveProducts || 0,
            totalUnits:       Math.max(0, rawStats.totalUnits || 0),
            inventoryValue:   Math.max(0, rawStats.inventoryValue || 0)
        };

        // Normalise recent activity rows into the shape the view expects
        const recentActivities = recentRows.map(row => ({
            type:        row.movement_type || row.type || '',
            quantity:    row.quantity,
            productName: row.product_name,
            timestamp:   row.date || row.created_at
        }));

        res.render('dashboard', {
            user:             req.session.user,
            stats,
            lowStockProducts,
            recentActivities
        });

    } catch (error) {
        console.error('Dashboard Data Error:', error);
        res.status(500).send('Error loading dashboard data.');
    }
});

module.exports = router;
