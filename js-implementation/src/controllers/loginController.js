const userModel = require('../models/userModels');
const bcrypt = require('bcryptjs');

const requireAuth = (req, res, next) => {
    if (req.session && req.session.user) {
        return next();
    }
    return res.redirect('/login');
};

const authController = { 
    getLogin: (req, res) => {
        if (req.session && req.session.user) {
            return res.redirect('/dashboard');
        }

        // 👇 Read the URL parameter (like $_GET['error'] in PHP)
        let errorMessage = null;
        if (req.query.error === 'unauthorized') {
            errorMessage = "Please log in first to access the app.";
        }

        res.render('login', { error: errorMessage }); 
    },

    postLogin: async (req, res) => {
        const { username_email, password } = req.body;
        
        try {
            const user = await userModel.findByUsernameOrEmail(username_email);
            
            if (!user) {
                //  Custom message 1
                return res.render('login', { error: 'Error: The username/email or password is incorrect.' });
            }

            //  Custom message 2: The Deactivated Check
            if (user.is_active == 0) {
                return res.render('login', { error: 'Account Suspended: This account has been deactivated. Please contact your system administrator.' });
            }

            const passwordMatch = await bcrypt.compare(password, user.password_hash);
            if (!passwordMatch) {
                //  Custom message 1
                return res.render('login', { error: 'Error: The username/email or password is incorrect.' });
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
            // Custom message 3
            return res.render('login', { error: 'An unexpected server error occurred.' });
        }
    },

    logout: (req, res) => {
        res.clearCookie('prism_session', { path: '/' });
        res.cookie('logout_flag', 'true', { maxAge: 5000, httpOnly: true });

        if (req.session) {
            req.session.user = null;
            req.session.destroy((err) => {
                if (err) {
                    console.error('Session destruction issue:', err);
                    return res.redirect('/dashboard');
                }
                return res.redirect('/logout-success');
            });
        } else {
            return res.redirect('/logout-success');
        }
    },

    getLogoutSuccess: (req, res) => {
        if (!req.cookies || !req.cookies.logout_flag) {
            return res.render('logout');
        }
        
        res.clearCookie('logout_flag');
        return res.render('logout');
    }
};

module.exports = authController;