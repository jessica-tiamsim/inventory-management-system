// src/routes/dashboardRoutes.js
const express = require('express');
const router = express.Router();

const productModel = require('../models/productModel');
const stockMovementModel = require('../models/stockMovementModel');

// Notice this is '/', which maps to '/dashboard' because of index.js!
router.get('/', async (req, res) => { 
    if (!res.locals.user) {
        return res.redirect('/login?error=unauthorized');
    }
    
    try {
        const [stats, lowStockProducts, recentActivities] = await Promise.all([
            productModel.getDashboardStats(),
            productModel.getLowStockProducts(),
            stockMovementModel.getRecentActivity()
        ]);

        const dashboardData = {
            stats: stats,
            lowStockProducts: lowStockProducts,
            recentActivities: recentActivities
        };

        res.render('dashboard', dashboardData);

    } catch (error) {
        console.error("Dashboard Data Error:", error);
        res.status(500).send("Error loading dashboard data.");
    }
});

module.exports = router;