const productModel = require('../models/productModel');

const productController = {
    /**
     * Compiles data request payloads derived cleanly from URL filter sets
     */
    getProductsPage: async (req, res) => {
        try {
            const searchTerms    = req.query.search          || '';
            const categoryFilter = req.query.category_filter || '';
            const statusFilter   = req.query.status_filter   || '2';
            const page           = Math.max(1, parseInt(req.query.page) || 1);
            const limit          = 10;
            const offset         = (page - 1) * limit;

            const { rows: products, total } = await productModel.getFilteredProducts(
                searchTerms, categoryFilter, statusFilter, limit, offset
            );
            const categoriesList = await productModel.getAllCategories();
            const totalPages     = Math.max(1, Math.ceil(total / limit));

            res.render('products', {
                products,
                categories:          categoriesList,
                currentSearch:       searchTerms,
                currentCategoryFilter: categoryFilter,
                currentStatusFilter: statusFilter,
                currentPage:         page,
                totalPages,
                currentPath:         '/products'
            });
        } catch (err) {
            console.error('Failed processing catalog view matrices:', err);
            res.status(500).send('Internal Server Error loading products.');
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
            category_id = category_id && category_id.trim() !== '' ? parseInt(category_id, 10) : null;
            unit_cost = unit_cost !== '' ? parseFloat(unit_cost) : 0.00;
            unit_price = unit_price !== '' ? parseFloat(unit_price) : 0.00;
            reorder_threshold = reorder_threshold !== '' ? parseInt(reorder_threshold, 10) : 10;

            await productModel.createProduct(sku, name, description, category_id, unit_cost, unit_price, reorder_threshold, supplier_name);
            res.redirect('/products');
        } catch (err) {
            console.error('Database insertion error:', err);
            res.status(500).send('Failed adding product to database.');
        }
    },

    /**
     * Updates an existing product (SKU is read-only)
     */
    postEditProduct: async (req, res) => {
        try {
            let { id, name, description, category_id, unit_cost, unit_price, reorder_threshold, supplier_name } = req.body;

            name = name ? name.trim() : null;
            description = description && description.trim() !== '' ? description.trim() : null;
            supplier_name = supplier_name && supplier_name.trim() !== '' ? supplier_name.trim() : null;
            category_id = category_id && category_id !== '' ? parseInt(category_id, 10) : null;
            unit_cost = parseFloat(unit_cost) || 0;
            unit_price = parseFloat(unit_price) || 0;
            reorder_threshold = parseInt(reorder_threshold, 10) || 0;

            await productModel.updateProduct(parseInt(id, 10), name, description, category_id, unit_cost, unit_price, reorder_threshold, supplier_name);
            res.redirect('/products');
        } catch (err) {
            console.error('Product update error:', err);
            res.status(500).send('Failed updating product.');
        }
    },

    /**
     * Soft-deletes (marks inactive) a product
     */
    postDeleteProduct: async (req, res) => {
        try {
            const id = parseInt(req.body.id, 10);
            await productModel.softDeleteProduct(id);
            res.redirect('/products');
        } catch (err) {
            console.error('Product delete error:', err);
            res.status(500).send('Failed inactivating product.');
        }
    }
};

module.exports = productController;