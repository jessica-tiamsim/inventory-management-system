const topMoversModel = require('../models/topMoversModel');

const topMoversController = {
    getReport: async (req, res) => {
        try {
            const startDate = req.query.start_date || '';
            const endDate = req.query.end_date || '';
            const isExport = req.query.export === 'csv';

            // Gather structural metrics
            const moversData = await topMoversModel.getTopMovers(startDate, endDate);

            // Handle clean data exports
            if (isExport) {
                let csv = 'SKU,Product Name,Category,Units Out\n';
                
                moversData.forEach(item => {
                    const name = `"${(item.product_name || '').replace(/"/g, '""')}"`;
                    const category = `"${(item.category_name || 'Unassigned').replace(/"/g, '""')}"`;
                    csv += `${item.sku},${name},${category},${item.units_out}\n`;
                });

                res.setHeader('Content-Type', 'text/csv');
                res.setHeader('Content-Disposition', 'attachment; filename="top_movers_report.csv"');
                return res.status(200).send(csv);
            }

            res.render('reports/top_movers', {
                moversData,
                startDate,
                endDate
            });

        } catch (error) {
            console.error("Error generating top movers report module:", error);
            res.status(500).send("Internal Server Error loading top movers.");
        }
    }
};

module.exports = topMoversController;