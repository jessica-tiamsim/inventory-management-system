const userModel = require('../models/userModel');
const bcrypt = require('bcryptjs');

exports.login = async (req, res) => {
    const { username_email, password } = req.body;
    console.log(`LOGIN ATTEMPT RECEIVED - Input User: "${username_email}"`);

    try {
        const user = await userModel.findByCredentials(username_email);
        
        if (!user) {
            console.log("Authentication Failed: User identity not found.");
            req.session.loginError = 'Invalid username/email or password.';
            return res.redirect('/login');
        }

        const isMatch = await bcrypt.compare(password, user.password_hash);
        
        if (!isMatch) {
            console.log("Authentication Failed: Password hash mismatch.");
            req.session.loginError = 'Invalid username/email or password.';
            return res.redirect('/login');
        }

        // Establish session on verified access match
        req.session.user = {
            id: user.id,
            username: user.username,
            role: user.role
        };

        console.log(`Authentication Success! Redirecting ${user.username} to dashboard.`);
        
        return res.redirect('/dashboard');

    } catch (err) {
        console.error("Authentication Critical Exception Logs: ", err);
        req.session.loginError = 'System connection breakdown. Please try again.';
        return res.redirect('/login');
    }
};

exports.logout = (req, res) => {
    req.session.destroy((err) => {
        if (err) console.error("Session destruction failure:", err);
        res.redirect('/login');
    });
};

