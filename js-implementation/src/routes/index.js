// src/routes/index.js
const express = require('express');
const router = express.Router();

// 1. Import your modular route files
const loginRoutes = require('./loginRoutes');
const dashboardRoutes = require('./dashboardRoutes');
const productsRoutes = require('./productsRoutes');
const profileRoutes = require('./profileRoutes');
const stockMovementRoutes = require('./stockmovementRoutes');


// 2. Delegate the traffic!
// Anything related to auth (login, logout) goes to loginRoutes.js
router.use('/', loginRoutes); 

// Anything related to employee management (/profile, /users/add) goes to profileRoutes.js
router.use('/', profileRoutes); 

// Anything starting with /dashboard goes to dashboardRoutes.js
router.use('/dashboard', dashboardRoutes); 

// Anything starting with /products goes to productsRoutes.js
router.use('/products', productsRoutes); 

router.use('/stock_movement', stockMovementRoutes); 

module.exports = router;