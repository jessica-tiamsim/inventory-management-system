const stockModel = require('../models/stockmovementModel');
const productModel = require('../models/productModel');

const stockController = {
    getStockMovements: async (req, res) => {
        try {
            console.log("🟢 Made it to getStockMovements controller!");
            const skuFilter = req.query.sku || 'all';
            const typeFilter = req.query.type || 'all';
            
            // Simultaneously fetch options for modal dropdown selector rules
            const productsList = await productModel.getAllActiveProducts();
            const matchingMovements = await stockModel.getMovementsByFilters(skuFilter, typeFilter);

            res.render('stock_movement', {
                products: productsList,
                movements: matchingMovements,
                skuFilter: skuFilter,
                typeFilter: typeFilter
            });
        } catch (err) {
            console.error('Failure rendering transaction ledger panel layout:', err);
            res.status(500).send("Internal Database Server Exception.");
        }
    },

    postRecordMovement: async (req, res) => {
    try {
        console.log("🟢 Made it to postRecordMovement controller!", req.body);
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