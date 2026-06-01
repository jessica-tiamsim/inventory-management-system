const express = require('express');
const router = express.Router();
const movementLedgerController = require('../controllers/movement_ledger_controller');
const { verifySession } = require('../middlewares/authMiddleware');

router.get('/', verifySession, movementLedgerController.getReport);

module.exports = router;
