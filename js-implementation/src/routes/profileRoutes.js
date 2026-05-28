const express = require('express');
const router = express.Router();
const profileController = require('../controllers/profile_controller');

/**
 * Route safeguard middleware tracking user session parameters 
 */
const verifySession = (req, res, next) => {
    if (req.session && req.session.user) {
        return next();
    }
    res.redirect('/login?error=unauthorized');
};

// --- Protected Administrative Resource Endpoints ---
router.get('/profile', verifySession, profileController.getProfile);
router.post('/users/add', verifySession, profileController.postCreateUser);

module.exports = router;