// src/controllers/login_controller.js
const userModel = require('../models/userModels');
const bcrypt = require('bcryptjs'); // Fixed: using bcryptjs

const authController = { 
    // Render presentation layout view
    getLogin: (req, res) => {
        if (req.session && req.session.user) {
            return res.redirect('/products');
        }
        // Fixed: pointing to auth/login (adjust if your file is somewhere else!)
        res.render('auth/login', { error: null });
    },

    // Process submission payloads
    postLogin: async (req, res) => {
        const { username_email, password } = req.body;
        
        try {
            const user = await userModel.findByUsernameOrEmail(username_email);
            if (!user) {
                return res.render('/login', { error: 'Invalid authentication credentials provided.' });
            }

            const passwordMatch = await bcrypt.compare(password, user.password_hash);
            if (!passwordMatch) {
                return res.render('/login', { error: 'Invalid authentication credentials provided.' });
            }

            req.session.user = {
                id: user.id,
                username: user.username,
                role: user.role 
            };

            // Fixed: Explicitly save session before bouncing to the inventory dashboard
            req.session.save((err) => {
                if (err) console.error('Session save error:', err);
                return res.redirect('/dashboard');
            });
            
        } catch (err) {
            console.error('System validation runtime drop:', err);
            return res.render('/login', { error: 'Internal structural system error. Please retry.' });
        }
    },

    // Invalidate sessions tracking details
    logout: (req, res) => {
        req.session.destroy((err) => {
            if (err) console.error('Session destruction issue:', err);
            res.redirect('/login'); // Fixed: Removed the double /login/login
        });
    }
};

module.exports = authController;