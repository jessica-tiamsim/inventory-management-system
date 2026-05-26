// src/routes/index.js
const express = require('express');
const router = express.Router();
const AuthController = require('../controllers/loginController');

// Base Route Redirect
router.get('/', (req, res) => res.redirect('/login'));

// Auth Routes
router.get('/login', AuthController.showLoginForm);
router.post('/login', AuthController.login);
router.get('/logout', AuthController.logout);

// Protected Dashboard Route
router.get('/dashboard', (req, res) => {
    // Check our global user variable (set in server.js) to ensure they are logged in
    if (!res.locals.user) {
        return res.redirect('/login');
    }
    
    // For now, we'll just send basic HTML until we build the dashboard view
    res.send(`
        <div style="font-family: sans-serif; padding: 2rem;">
            <h1>Welcome to the PRISM Dashboard</h1>
            <p>You are successfully logged in as: <strong>${res.locals.user.username}</strong></p>
            <p>Access Level: ${res.locals.user.role.toUpperCase()}</p>
            <br>
            <a href="/logout" style="padding: 10px 15px; background: #ff4757; color: white; text-decoration: none; border-radius: 5px;">Secure Logout</a>
        </div>
    `);
});

console.log("End")
module.exports = router;