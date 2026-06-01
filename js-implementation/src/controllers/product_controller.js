const productModel = require('../models/productModel');

const productController = {
    /**
     * Compiles data request payloads derived cleanly from URL filter sets
     */
    getProductsPage: async (req, res) => {
        try {
            // Read state parameters directly from the single-form query payload
            const searchTerms = req.query.search || '';
            const productFilter = req.query.products_filter || 'all';

            // Query utilizing our single, updated filtration model method
            const filteredResults = await productModel.getFilteredProducts(searchTerms, productFilter);

            // Fetch structural categories profile ONLY to map add product creation choices modal dropdown
            const categoriesList = await productModel.getAllCategories(); 

            const activeProductsList = await productModel.getAllActiveProducts();
            
            // Pass values directly into our EJS template context engine
            res.render('products', {
                products: filteredResults,
                categories: categoriesList,
                allProducts: activeProductsList,
                currentSearch: searchTerms,
                currentProductFilter: productFilter,
                currentPath: '/products'
            });
        } catch (err) {
            console.error('Failed processing catalog view matrices:', err);
            res.status(500).send('Internal Server Error loading products matrix layout logs.');
        }
    },

    /**
     * Sanitizes data parameters dropped from client-side forms to safely save models
     */
    postCreateProduct: async (req, res) => {
        try {
            let { sku, name, description, category_id, unit_cost, unit_price, reorder_threshold, supplier_name } = req.body;

            sku = sku ? sku.trim() : null;
            name = name ? name.trim() : null;
            description = description && description.trim() !== '' ? description.trim() : null;
            supplier_name = supplier_name && supplier_name.trim() !== '' ? supplier_name.trim() : null;

            category_id = category_id && category_id.trim() !== '' && category_id !== '#' ? parseInt(category_id, 10) : null;
            
            unit_cost = unit_cost && unit_cost !== '' ? parseFloat(unit_cost) : 0.00;
            unit_price = unit_price && unit_price !== '' ? parseFloat(unit_price) : 0.00;
            reorder_threshold = reorder_threshold && reorder_threshold !== '' ? parseInt(reorder_threshold, 10) : 10;

            await productModel.createProduct(sku, name, description, category_id, unit_cost, unit_price, reorder_threshold, supplier_name);
            
            res.redirect('/products');
        } catch (err) {
            console.error('Database insertion error dropped during submission:', err);
            res.status(500).send('Failed adding item record down to database inventory tables.');
        }
    }
};

module.exports = productController;