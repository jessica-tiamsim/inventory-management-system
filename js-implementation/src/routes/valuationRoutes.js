const express = require('express');
const router = express.Router();
const valuationController = require('../controllers/valuationController');

router.get('/', valuationController.getReport);

module.exports = router;