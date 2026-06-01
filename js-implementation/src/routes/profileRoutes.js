const express = require('express');
const router = express.Router();
const profileController = require('../controllers/profile_controller');
const { verifySession } = require('../middlewares/authMiddleware');

// --- Protected Administrative Resource Endpoints ---
router.get('/profile',      verifySession, profileController.getProfile);
router.post('/profile/add', verifySession, profileController.postCreateUser);

module.exports = router;
