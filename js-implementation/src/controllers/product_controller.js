const productModel = require('../models/productModel');
// const categoryModel = require('../models/categoryModel'); 

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

            // const categoriesList = await categoryModel.getAllCategories(); 

            res.render('products', {
                products: results,
                categories: [], // Replace with categoriesList when ready
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
            let { sku, name, description, category_id, unit_cost, unit_price, reorder_threshold, supplier_name } = req.body;

            sku = sku ? sku.trim() : null;
            name = name ? name.trim() : null;
            description = description && description.trim() !== '' ? description.trim() : null;
            supplier_name = supplier_name && supplier_name.trim() !== '' ? supplier_name.trim() : null;

            category_id = category_id && category_id.trim() !== '' && category_id !== '#' ? parseInt(category_id, 10) : null;
            
            // Force numeric formats so empty inputs don't crash MySQL decimals
            unit_cost = unit_cost && unit_cost !== '' ? parseFloat(unit_cost) : 0.00;
            unit_price = unit_price && unit_price !== '' ? parseFloat(unit_price) : 0.00;
            reorder_threshold = reorder_threshold && reorder_threshold !== '' ? parseInt(reorder_threshold, 10) : 10;

            await productModel.createProduct(sku, name, description, category_id, unit_cost, unit_price, reorder_threshold, supplier_name);
            res.redirect('/products');
        } catch (err) {
            console.error('Database insertion error dropped:', err);
            res.status(500).send('Failed adding item record down to database logs.');
        }
    }
};

module.exports = productController;