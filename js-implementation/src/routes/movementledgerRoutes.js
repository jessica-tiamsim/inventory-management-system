// src/routes/reports.js
const express = require('express');
const router = express.Router();
const movementLedgerController = require('../controllers/movement_ledger_controller');

router.get('/', movementLedgerController.getReport);

module.exports = router;