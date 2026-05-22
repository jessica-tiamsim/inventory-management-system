const bcrypt = require('bcrypt');
const UserModel = require('../models/userModel');
const logger = require('../../config/logger');

const AuthController = {
    showLoginForm(req, res) {
        res.render('auth/login', { error: null });
    },

    async handleLogin(req, res) {
        const { username, password } = req.body;

        try {
            // 1. Search DB for matching user identifiers
            const user = await UserModel.findByUsernameOrEmail(username);
            if (!user) {
                logger.warn(`Security Warning: Unauthorized access attempt for identifier: "${username}"`);
                return res.render('auth/login', { error: 'Invalid username or password.' });
            }

            // 2. Enforce your explicit status flag restrictions
            if (user.is_active !== 1) {
                logger.warn(`Access Blocked: Suspended profile account "${username}" attempted authentication.`);
                return res.render('auth/login', { error: 'Your account is deactivated.' });
            }

            // 3. Verify the cryptographic password hash match
            const passwordMatches = await bcrypt.compare(password, user.password_hash);
            if (!passwordMatches) {
                logger.warn(`Authentication Failure: Password mismatch recorded for user: "${username}"`);
                return res.render('auth/login', { error: 'Invalid username or password.' });
            }

            // 4. Complete Session Initialization
            req.session.userId = user.id;
            req.session.username = user.username;
            req.session.email = user.email;
            req.session.role = user.role; 

            logger.info(`Session Authorized: User "${user.username}" logged in. [Role Scope: ${user.role.toUpperCase()}]`);
            res.redirect('/dashboard');

        } catch (err) {
            logger.error('Critical Error inside Authentication Controller Subsystem', err);
            res.status(500).render('auth/login', { error: 'An internal server error occurred.' });
        }
    },

    handleLogout(req, res) {
        const activeUser = req.session.username || 'Guest';
        req.session.destroy((err) => {
            if (err) {
                logger.error(`Session destruction fault for user ${activeUser}: ${err.message}`);
                return res.redirect('/dashboard');
            }
            logger.info(`Session closed: User "${activeUser}" logged out cleanly.`);
            res.redirect('/login');
        });
    }
};

module.exports = AuthController;