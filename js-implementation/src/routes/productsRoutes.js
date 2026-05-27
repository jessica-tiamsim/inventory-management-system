// src/routes/productsRoutes.js
const express = require('express');
const router = express.Router();

// This maps to '/products' 
router.get('/', (req, res) => {
    // Standard security check
    if (!res.locals.user) {
        return res.redirect('/login?error=unauthorized');
    }
    
    // Temporary response until we build products.ejs
    res.send('<h1>PRISM Inventory Management</h1><p>The products table will go here!</p>');
});

module.exports = router;