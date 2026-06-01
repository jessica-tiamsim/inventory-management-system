const movementLedgerModel = require('../models/movementLedgerModel');

const movementLedgerController = {
    getReport: async (req, res) => {
        try {
            const startDate = req.query.start_date || '';
            const endDate = req.query.end_date || '';
            const selectedProduct = req.query.product || 'all';
            const selectedType = req.query.movement_type || 'all';
            const isExport = req.query.export === 'csv';

            // Set up pagination rules
            const currentPage = parseInt(req.query.page) || 1;
            const limit = 10; // Change to your preferred rows per page (e.g., 10, 25, 50)
            const offset = (currentPage - 1) * limit;

            // Fetch rows (If it's a CSV export, skip limits to download everything)
            const movements = await movementLedgerModel.getMovementLedger(
                startDate, endDate, selectedProduct, selectedType, 
                isExport ? null : limit, 
                isExport ? null : offset
            );
            
            const products = await movementLedgerModel.getAllProductsList();
            const totalCount = await movementLedgerModel.getMovementLedgerCount(startDate, endDate, selectedProduct, selectedType);
            const totalPages = Math.ceil(totalCount / limit);

            if (isExport) {
                let csv = 'Date & Time,SKU,Product Name,Type,Quantity,Recorded By,Notes\n';
                movements.forEach(row => {
                    const formattedDate = new Date(row.date_time).toLocaleString('en-US', { 
                        month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true 
                    }).replace(/,/g, ''); 
                    
                    const name = `"${(row.product_name || '').replace(/"/g, '""')}"`;
                    const typeLabel = row.movement_type === 'in' ? 'Stock-In' : row.movement_type === 'out' ? 'Stock-Out' : 'Adjustment';
                    const qtySign = (row.movement_type === 'in' || (row.movement_type === 'adjustment' && row.quantity > 0)) ? '+' : '';
                    const recorder = `"${(row.recorded_by || 'System').replace(/"/g, '""')}"`;
                    const notes = `"${(row.notes || '--').replace(/"/g, '""')}"`;

                    csv += `${formattedDate},${row.sku},${name},${typeLabel},${qtySign}${row.quantity},${recorder},${notes}\n`;
                });

                res.setHeader('Content-Type', 'text/csv');
                res.setHeader('Content-Disposition', 'attachment; filename="movement_ledger_report.csv"');
                return res.status(200).send(csv);
            }

            res.render('reports/movement_ledger', {
                movements,
                startDate,
                endDate,
                products,
                selectedProduct,
                selectedType,
                currentPage,
                totalPages
            });
        } catch (error) {
            console.error("Error generating movement ledger report:", error);
            res.status(500).send("Internal Server Error loading movement ledger.");
        }
    }
};

module.exports = movementLedgerController;