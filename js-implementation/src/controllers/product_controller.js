const productModel = require('../models/productModel');

const productController = {
    getProductsPage: async (req, res) => {
        try {
            const searchTerms = req.query.search || '';
            let results;

            if (searchTerms) {
                results = await productModel.searchProducts(searchTerms);
            } else {
                results = await productModel.getAllProducts();
            }

            res.render('products', {
                products: results,
                currentSearch: searchTerms,
                currentPath: '/products'
            });
        } catch (err) {
            console.error('Failed processing catalog data:', err);
            res.status(500).send('Internal Server Error loading products matrix.');
        }
    },

    postCreateProduct: async (req, res) => {
        try {
            const { sku, name, description, category, unit_cost, unit_price, reorder_threshold, supplier_name } = req.body;

            await productModel.createProduct(sku, name, description, category, unit_cost, unit_price, reorder_threshold, supplier_name);
            res.redirect('/products');
        } catch (err) {
            console.error('Database insertion error dropped:', err);
            res.status(500).send('Failed adding item record down to database logs.');
        }
    }
};

module.exports = productController;