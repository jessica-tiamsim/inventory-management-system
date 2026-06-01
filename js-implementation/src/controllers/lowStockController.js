const LowStockModel = require('../models/LowStockModel');

const lowStockController = {
    getReport: async (req, res) => {
        try {
            const productId = req.query.product || 'all';
            const isExport = req.query.export === 'csv';
            
            // Set up pagination parameters
            const page = parseInt(req.query.page) || 1;
            const limit = 10; // Adjust this value to alter records shown per page view
            const offset = (page - 1) * limit;

            // Fetch data from DB
            const products = await LowStockModel.getProductsList();

            // Handle CSV Export (Bypass layout pagination controls)
            if (isExport) {
                const { rows: allLowStockData } = await LowStockModel.getLowStockProducts(productId, null, 0);
                
                let csv = 'SKU,Product Name,Category,Quantity,Reorder Threshold,Supplier Name\n';
                allLowStockData.forEach(item => {
                    const name = `"${(item.name || '').replace(/"/g, '""')}"`;
                    const supplier = `"${(item.supplier_name || '').replace(/"/g, '""')}"`;
                    const category = `"${(item.category_name || '').replace(/"/g, '""')}"`;
                    
                    csv += `${item.sku},${name},${category},${item.current_quantity},${item.reorder_threshold},${supplier}\n`;
                });

                res.setHeader('Content-Type', 'text/csv');
                res.setHeader('Content-Disposition', 'attachment; filename="low_stock_report.csv"');
                return res.status(200).send(csv);
            }

            // Normal browser UI view query paths
            const { rows: lowStockData, total } = await LowStockModel.getLowStockProducts(productId, limit, offset);
            const totalPages = Math.ceil(total / limit);

            res.render('reports/low_stock', {
                products, 
                lowStockData,
                selectedProduct: productId,
                currentPage: page,
                totalPages: totalPages
            });

        } catch (error) {
            console.error("Error generating low stock report:", error);
            res.status(500).send("Internal Server Error loading report.");
        }
    }
};

module.exports = lowStockController;