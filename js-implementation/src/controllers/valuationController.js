const ValuationModel = require('../models/ValuationModel');

const valuationController = {
    getReport: async (req, res) => {
        try {
            const sortBy = req.query.sort || 'value';
            const isExport = req.query.export === 'csv';

            // Fetch valuation data array from model
            const valuationData = await ValuationModel.getInventoryValuation(sortBy);

            // Calculate grandTotal for the EJS template footer
            const grandTotal = valuationData.reduce((sum, item) => sum + Number(item.total_value || 0), 0);

            // Handle CSV Export
            if (isExport) {
                let csv = 'Category,Value\n';
                valuationData.forEach(item => {
                    const category = `"${(item.category_name || 'Unassigned').replace(/"/g, '""')}"`;
                    csv += `${category},${Number(item.total_value || 0).toFixed(2)}\n`;
                });
                csv += `Total,${grandTotal.toFixed(2)}\n`;

                res.setHeader('Content-Type', 'text/csv');
                res.setHeader('Content-Disposition', 'attachment; filename="inventory_valuation_report.csv"');
                return res.status(200).send(csv);
            }

            // Render your newly fixed reports/valuation.ejs file
            // Note: NO leading slash before reports!
            res.render('reports/valuation', {
                valuationData,
                sortBy,
                grandTotal
            });

        } catch (error) {
            console.error("Error generating inventory valuation report:", error);
            res.status(500).send("Internal Server Error loading valuation details.");
        }
    }
};

module.exports = valuationController;