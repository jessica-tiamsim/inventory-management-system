const express = require('express');
const router = express.Router();
const topMoversController = require('../controllers/top_movers_controller');

router.get('/', topMoversController.getReport);

module.exports = router;