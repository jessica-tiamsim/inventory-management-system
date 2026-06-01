const express = require('express');
const router = express.Router();
const valuationController = require('../controllers/valuationController');
const { verifySession } = require('../middlewares/authMiddleware');

router.get('/', verifySession, valuationController.getReport);

module.exports = router;
