const express = require('express');
const router = express.Router();
const authRoutes = require('./auth');

// Mount sub-routing pipelines cleanly
router.use('/auth', authRoutes);

router.get('/', (req, res) => {
    // Safely check if both the session AND the user object exist
    if (req.session && req.session.user) {
        return res.redirect('/dashboard');
    }
    // If no session exists, smoothly redirect to login
    return res.redirect('/login');
});

// Render Login Screen Portal Layer
router.get('/login', (req, res) => {
    if (req.session && req.session.user) {
        return res.redirect('/dashboard');
    }
    res.render('login');
});

router.get('/dashboard', (req, res) => {
    // Security wall to catch unauthenticated manual URL injections
    if (!req.session || !req.session.user) {
        console.log("Unauthorized access attempt blocked on /dashboard.");
        return res.redirect('/login');
    }
    
    res.render('dashboard', { user: req.session.user });
});

module.exports = router;