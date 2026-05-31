const movementLedgerModel = require('../models/movementLedgerModel');

const movementLedgerController = {
    getReport: async (req, res) => {
        try {
            const startDate = req.query.start_date || '';
            const endDate = req.query.end_date || '';
            const selectedProduct = req.query.product || 'all';
            const selectedType = req.query.movement_type || 'all';
            const isExport = req.query.export === 'csv';

            // 1. Fetch dynamic filtered results directly from our queries
            const movements = await movementLedgerModel.getMovementLedger(startDate, endDate, selectedProduct, selectedType);
            
            // 2. Fetch the product choices to keep our filter lists updated
            const products = await movementLedgerModel.getAllProductsList();

            // 3. BACKEND CSV GENERATION MATCHING TOP MOVERS PATTERN
            if (isExport) {
                let csv = 'Date & Time,SKU,Product Name,Type,Quantity,Recorded By,Notes\n';
                
                movements.forEach(row => {
                    const formattedDate = new Date(row.date_time).toLocaleString('en-US', { 
                        month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true 
                    }).replace(/,/g, ''); // Clear commas from dates so columns don't break
                    
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

            // 4. Fallback rendering for the regular page view
            res.render('reports/movement_ledger', {
                movements,
                startDate,
                endDate,
                products,
                selectedProduct,
                selectedType
            });
        } catch (error) {
            console.error("Error generating movement ledger report:", error);
            res.status(500).send("Internal Server Error loading movement ledger.");
        }
    }
};

module.exports = movementLedgerController;