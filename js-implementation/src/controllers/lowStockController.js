const LowStockModel = require('../models/LowStockModel');

const lowStockController = {
    getReport: async (req, res) => {
        try {
            const categoryId = req.query.category || 'all';
            const isExport = req.query.export === 'csv';

            // Fetch data from DB
            const categories = await LowStockModel.getCategories();
            const lowStockData = await LowStockModel.getLowStockProducts(categoryId);

            // Handle CSV Export
            if (isExport) {
                let csv = 'SKU,Product Name,Category,Quantity,Reorder Threshold,Supplier Name\n';
                
                lowStockData.forEach(item => {
                    // Escape quotes and commas in strings
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
                categories,
                lowStockData,
                selectedCategory: categoryId
            });

        } catch (error) {
            console.error("Error generating low stock report:", error);
            res.status(500).send("Internal Server Error loading report.");
        }
    }
};

module.exports = lowStockController;