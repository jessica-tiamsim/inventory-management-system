const express = require('express');
const router = express.Router();
const lowStockController = require('../controllers/lowStockController');

// GET /reports/low_stock
router.get('/', lowStockController.getReport);

module.exports = router;