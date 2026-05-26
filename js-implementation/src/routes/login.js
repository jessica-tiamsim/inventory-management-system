const express = require('express');
const router = express.Router();
const loginController = require('../controllers/loginController');
const { loginSchema } = require('../validators/loginValidator');

// Interceptor validating data formatting matches prior to executing database workflows
const validateLogin = (req, res, next) => {
    const { error } = loginSchema.validate(req.body);
    if (error) {
        // Safely catch structural flaws and present them inside .forms_description
        return res.render('login', { error: error.details[0].message });
    }
    next();
};

// Route mapping tracking actions to endpoints
router.get('/login', loginController.getLogin);
router.post('/login', validateLogin, loginController.postLogin);
router.get('/logout', loginController.logout);

module.exports = router;