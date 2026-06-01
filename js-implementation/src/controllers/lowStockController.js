const LowStockModel = require('../models/LowStockModel');
const productModel  = require('../models/productModel');

const lowStockController = {
    getReport: async (req, res) => {
        try {
            const selectedCategory = req.query.category || 'all';
            const isExport = req.query.export === 'csv';

            const page   = parseInt(req.query.page) || 1;
            const limit  = 10;
            const offset = (page - 1) * limit;

            // Category list for the filter dropdown
            const categories = await productModel.getAllCategories();

            if (isExport) {
                const { rows: allData } = await LowStockModel.getLowStockProducts(selectedCategory, null, 0);

                let csv = 'SKU,Product Name,Category,Quantity,Reorder Threshold,Supplier Name\n';
                allData.forEach(item => {
                    const name     = `"${(item.name          || '').replace(/"/g, '""')}"`;
                    const supplier = `"${(item.supplier_name || '').replace(/"/g, '""')}"`;
                    const category = `"${(item.category_name || '').replace(/"/g, '""')}"`;
                    csv += `${item.sku},${name},${category},${item.current_quantity},${item.reorder_threshold},${supplier}\n`;
                });

                res.setHeader('Content-Type', 'text/csv');
                res.setHeader('Content-Disposition', 'attachment; filename="low_stock_report.csv"');
                return res.status(200).send(csv);
            }

            const { rows: lowStockData, total } = await LowStockModel.getLowStockProducts(selectedCategory, limit, offset);
            const totalPages = Math.ceil(total / limit);

            res.render('reports/low_stock', {
                categories,
                lowStockData,
                selectedCategory,
                currentPage:  page,
                totalPages
            });

        } catch (error) {
            console.error('Error generating low stock report:', error);
            res.status(500).send('Internal Server Error loading report.');
        }
    }
};

module.exports = lowStockController;
