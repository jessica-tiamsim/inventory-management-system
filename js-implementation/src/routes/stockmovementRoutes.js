const express = require('express');
const router = express.Router();
const stockController = require('../controllers/stock_movement_controller');
const { verifySession } = require('../middlewares/authMiddleware');

router.get('/', verifySession, stockController.getStockMovements);
router.post('/add', verifySession, stockController.postRecordMovement);

module.exports = router;
