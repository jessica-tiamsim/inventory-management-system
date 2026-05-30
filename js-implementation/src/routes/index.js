// src/routes/index.js
const express = require('express');
const router = express.Router();

// 1. Import your modular route files
const loginRoutes = require('./loginRoutes');
const dashboardRoutes = require('./dashboardRoutes');
const productsRoutes = require('./productsRoutes');
const profileRoutes = require('./profileRoutes');
const stockMovementRoutes = require('./stockmovementRoutes');
const lowStockRoutes = require('./lowStockRoutes');
const valuationRoutes = require('./valuationRoutes');
const movementLedgerRoutes = require('./movementledgerRoutes');
const topMoversRoutes = require('./topmoversRoutes');

router.get('/', (req, res) => {
    res.redirect('/login');
});

// 1. Delegate the traffic!
// Anything related to auth (login, logout) goes to loginRoutes.js
router.use('/', loginRoutes); 

// Anything related to employee management (/profile, /users/add) goes to profileRoutes.js
router.use('/', profileRoutes); 

// Anything starting with /dashboard goes to dashboardRoutes.js
router.use('/dashboard', dashboardRoutes); 

// Anything starting with /products goes to productsRoutes.js
router.use('/products', productsRoutes); 

//
router.use('/stock_movement', stockMovementRoutes); 

//
router.use('/reports/low_stock', lowStockRoutes);

//
router.use('/reports/valuation', valuationRoutes);

//
router.use('/reports/movement_ledger', movementLedgerRoutes);

//
router.use('/reports/top_movers', topMoversRoutes);


module.exports = router;