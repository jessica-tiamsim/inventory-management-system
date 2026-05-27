// src/routes/index.js
const express = require('express');
const router = express.Router();

// 👇 THE FIX: We are explicitly pointing to login_controller (with the underscore)
const authController = require('../controllers/loginController'); 
const { authValidator } = require('../validators/loginValidator'); 

// Base Route Redirect
router.get('/', (req, res) => res.redirect('/login'));

// Validation Middleware
const validateMiddleware = (req, res, next) => {
    const { error } = authValidator.validate(req.body);
    if (error) {
        return res.render('login', { error: error.details[0].message });
    }
    next();
};

// Route mapping tracking actions to endpoints
router.get('/login', authController.getLogin);
router.post('/login', validateMiddleware, authController.postLogin);
router.get('/logout', authController.logout);

// Protected Dashboard Route
router.get('/dashboard', (req, res) => {
    if (!res.locals.user) return res.redirect('/login');
    res.send(`
        <div style="font-family: sans-serif; padding: 2rem;">
            <h1>Welcome to the PRISM Dashboard</h1>
            <p>You are successfully logged in as: <strong>${res.locals.user.username}</strong></p>
            <p>Access Level: ${res.locals.user.role.toUpperCase()}</p>
            <a href="/logout" style="padding: 10px 15px; background: #ff4757; color: white; text-decoration: none; border-radius: 5px;">Secure Logout</a>
        </div>
    `);
});

module.exports = router;