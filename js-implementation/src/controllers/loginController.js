const userModel = require('../models/userModels');
const bcrypt = require('bcryptjs');

const authController = { 
    // 1. GET /login
    getLogin: (req, res) => {
        if (req.session && req.session.user) {
            return res.redirect('/dashboard');
        }
        res.render('auth/login', { error: null });
    },

    // 2. POST /login
    postLogin: async (req, res) => {
        const { username_email, password } = req.body;
        
        try {
            const user = await userModel.findByUsernameOrEmail(username_email);
            if (!user) {
                return res.render('auth/login', { error: 'Invalid authentication credentials provided.' });
            }

            const passwordMatch = await bcrypt.compare(password, user.password_hash);
            if (!passwordMatch) {
                return res.render('auth/login', { error: 'Invalid authentication credentials provided.' });
            }

            req.session.user = {
                id: user.id,
                username: user.username,
                role: user.role 
            };

            req.session.save((err) => {
                if (err) console.error('Session save error:', err);
                return res.redirect('/dashboard');
            });
            
        } catch (err) {
            console.error('System validation runtime drop:', err);
            return res.render('auth/login', { error: 'Internal structural system error. Please retry.' });
        }
    },

    // 3. GET /logout
    logout: (req, res) => {
        req.session.destroy((err) => {
            if (err) console.error('Session destruction issue:', err);
            res.redirect('/login'); 
        });
    }
};

module.exports = authController;