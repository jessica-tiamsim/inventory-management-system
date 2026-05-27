// src/routes/login.js
const express = require('express');
const router = express.Router();

const authController = require('../controllers/loginController');
const { authValidator } = require('../validators/loginValidator'); 

// Validator Middleware
const validateMiddleware = (req, res, next) => {
    const { error } = authValidator.validate(req.body);
    if (error) {
        return res.render('login', { error: error.details[0].message });
    }
    next();
};

// Auth Routes
router.get('/', (req, res) => res.redirect('/login'));
router.get('/login', authController.getLogin);
router.post('/login', validateMiddleware, authController.postLogin);
router.get('/logout', authController.logout);

module.exports = router;