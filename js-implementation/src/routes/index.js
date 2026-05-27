// src/routes/index.js
const express = require('express');
const router = express.Router();

// 1. Import your new modular route files
const loginRoutes = require('./loginRoutes');
const dashboardRoutes = require('./dashboardRoutes');
const productsRoutes = require('./productsRoutes');

// 2. Delegate the traffic!
// Anything related to auth goes to login.js
router.use('/', loginRoutes); 

// Anything starting with /dashboard goes to dashboardRoutes.js
router.use('/dashboard', dashboardRoutes); 

// Anything starting with /products goes to productsRoutes.js
router.use('/products', productsRoutes); 

module.exports = router;