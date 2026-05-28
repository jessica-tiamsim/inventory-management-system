// src/routes/productsRoutes.js
const express = require('express');
const router = express.Router();
const productController = require('../controllers/product_controller');

/**
 * Route safeguard middleware tracking user session parameters 
 */
const verifySession = (req, res, next) => {
    if (req.session && req.session.user) {
        return next();
    }
    res.redirect('/login?error=unauthorized');
};

// --- Protected Product Module Resource Endpoints ---

// Handles loading, searching, and viewing the product database table matrix
router.get('/', verifySession, productController.getProductsPage);

// Handles processing, validating, and saving the add-product overlay form metrics
router.post('/add', verifySession, productController.postCreateProduct);

module.exports = router;