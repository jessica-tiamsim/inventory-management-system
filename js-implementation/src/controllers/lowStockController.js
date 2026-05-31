const LowStockModel = require('../models/LowStockModel');

const lowStockController = {
    getReport: async (req, res) => {
        try {
            const productId = req.query.product || 'all';
            const isExport = req.query.export === 'csv';

            // Fetch data from DB
            const products = await LowStockModel.getProductsList();
            const lowStockData = await LowStockModel.getLowStockProducts(productId);

            // Handle CSV Export
            if (isExport) {
                let csv = 'SKU,Product Name,Category,Quantity,Reorder Threshold,Supplier Name\n';
                
                lowStockData.forEach(item => {
                    const name = `"${(item.name || '').replace(/"/g, '""')}"`;
                    const supplier = `"${(item.supplier_name || '').replace(/"/g, '""')}"`;
                    const category = `"${(item.category_name || '').replace(/"/g, '""')}"`;
                    
                    csv += `${item.sku},${name},${category},${item.current_quantity},${item.reorder_threshold},${supplier}\n`;
                });

                res.setHeader('Content-Type', 'text/csv');
                res.setHeader('Content-Disposition', 'attachment; filename="low_stock_report.csv"');
                return res.status(200).send(csv);
            }

            res.render('reports/low_stock', {
                products, // Variables updated from categories to products
                lowStockData,
                selectedProduct: productId
            });

        } catch (error) {
            console.error("Error generating low stock report:", error);
            res.status(500).send("Internal Server Error loading report.");
        }
    }
};

module.exports = lowStockController;