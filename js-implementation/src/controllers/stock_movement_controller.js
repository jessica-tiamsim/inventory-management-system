const stockModel = require('../models/stockmovementModel');
const productModel = require('../models/productModel');

const stockController = {
    getStockMovements: async (req, res) => {
        try {
            const skuFilter = req.query.sku || 'all';
            const typeFilter = req.query.type || 'all';
            
            // Set up pagination parameters
            const page = parseInt(req.query.page) || 1;
            const limit = 10; // Adjust this number to change item rows displayed per page
            const offset = (page - 1) * limit;
            
            // Simultaneously fetch options for modal dropdown selectors
            const productsList = await productModel.getAllActiveProducts();
            
            // Destructure paginated items and master total count from the unified model response
            const { rows: matchingMovements, total } = await stockModel.getMovementsByFilters(skuFilter, typeFilter, limit, offset);

            const totalPages = Math.ceil(total / limit);

            res.render('stock_movement', {
                products: productsList,
                movements: matchingMovements,
                skuFilter: skuFilter,
                typeFilter: typeFilter,
                currentPage: page,
                totalPages: totalPages
            });
        } catch (err) {
            console.error('Failure rendering transaction ledger panel layout:', err);
            res.status(500).send("Internal Database Server Exception.");
        }
    },

    postRecordMovement: async (req, res) => {
    try {
        const { product_id, type, quantity, notes } = req.body;
        const userId = req.session.user.id; 

        await stockModel.createTransaction(product_id, type, quantity, notes, userId);
        
        res.redirect('/stock_movement');
    } catch (err) {
        console.error(err);
        res.status(500).send("Transaction Error");
    }
}
};

module.exports = stockController;