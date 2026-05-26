const express = require('express');
const router = express.Router();
const authController = require('../controllers/authController');

// Route configurations linking form submittal to controller actions
router.post('/login', authController.login);
router.get('/logout', authController.logout);

module.exports = router;