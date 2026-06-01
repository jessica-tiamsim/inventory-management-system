const express = require('express');
const router = express.Router();
const lowStockController = require('../controllers/lowStockController');
const { verifySession } = require('../middlewares/authMiddleware');

// GET /reports/low_stock
router.get('/', verifySession, lowStockController.getReport);

module.exports = router;
