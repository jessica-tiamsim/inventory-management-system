const express = require('express');
const router = express.Router();
const authController = require('../controllers/loginController');
const { authValidator } = require('../validators/loginValidator'); 

// Interceptor validating data formatting matches prior to executing database workflows
const validateMiddleware = (req, res, next) => {
    const { error } = authValidator.validate(req.body);
    if (error) {
        // Safely catch structural flaws and present them inside .forms_description
        return res.render('auth/login', { error: error.details[0].message });
    }
    next();
};

// Route mapping tracking actions to endpoints
router.get('/login', authController.getLogin);
router.post('/login', validateMiddleware, authController.postLogin);
router.get('/logout', authController.logout);

module.exports = router;