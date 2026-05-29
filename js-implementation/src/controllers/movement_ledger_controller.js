const movementLedgerModel = require('../models/movementLedgerModel');

const movementLedgerController = {
    getReport: async (req, res) => {
        try {
            const startDate = req.query.start_date || '';
            const endDate = req.query.end_date || '';

            // Pull dynamic historical rows from its own isolated data model file
            const movements = await movementLedgerModel.getMovementLedger(startDate, endDate);

            res.render('reports/movement_ledger', {
                movements,
                startDate,
                endDate
            });
        } catch (error) {
            console.error("Error generating movement ledger report:", error);
            res.status(500).send("Internal Server Error loading movement ledger.");
        }
    }
};

module.exports = movementLedgerController;