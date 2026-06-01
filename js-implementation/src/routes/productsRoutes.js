// src/routes/productsRoutes.js
const express = require('express');
const router = express.Router();
const productController = require('../controllers/product_controller');
const { verifySession } = require('../middlewares/authMiddleware');
const { createProductValidator, editProductValidator } = require('../validators/productValidator');

// Validation middleware factory — renders the products page with an error on failure
const validateProduct = (schema) => async (req, res, next) => {
    const { error } = schema.validate(req.body, { abortEarly: true });
    if (error) {
        // Re-render the products page with the validation error message
        try {
            const productModel = require('../models/productModel');
            const products = await productModel.getFilteredProducts('', '', '2');
            const categories = await productModel.getAllCategories();
            return res.render('products', {
                products,
                categories,
                currentSearch: '',
                currentCategoryFilter: '',
                currentStatusFilter: '2',
                currentPath: '/products',
                validationError: error.details[0].message
            });
        } catch (err) {
            return res.status(400).send(error.details[0].message);
        }
    }
    next();
};

// --- Protected Product Module Resource Endpoints ---
router.get('/',        verifySession, productController.getProductsPage);
router.post('/add',    verifySession, validateProduct(createProductValidator), productController.postCreateProduct);
router.post('/edit',   verifySession, validateProduct(editProductValidator),   productController.postEditProduct);
router.post('/delete', verifySession, productController.postDeleteProduct);

module.exports = router;
