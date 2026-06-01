const express = require('express');
const router = express.Router();
const topMoversController = require('../controllers/top_movers_controller');
const { verifySession } = require('../middlewares/authMiddleware');

router.get('/', verifySession, topMoversController.getReport);

module.exports = router;
