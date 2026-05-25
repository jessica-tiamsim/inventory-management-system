const bcrypt = require('bcryptjs');
const pool = require('../../config/db');

const AuthController = {
    // 1. Render Login Screen from /views/auth/login.ejs
    showLoginForm: (req, res) => {
        // If user already logged in, bypass login screen
        if (req.session.userId) {
            return res.redirect('/dashboard');
        }
        res.render('auth/login', { error: null });
    },

    // 2. Process incoming credentials
    handleLogin: async (req, res) => {
        const { username_email: username, password } = req.body;

        try {
            // Find user matching username OR email
            const [users] = await pool.execute(
                'SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1',
                [username, username]
            );

            if (users.length === 0) {
                return res.render('auth/login', { error: 'Invalid username, email, or password.' });
            }

            const user = users[0];

// 1. Debug check to confirm the data structure (Optional)
        console.log("Password Hash From DB:", user.password_hash); 

// 2. FIXED: Reference 'password_hash' precisely to match your MySQL table layout
        const passwordMatches = await bcrypt.compare(password, user.password_hash);

        if (!passwordMatches) {
    return res.render('login', { error: 'Invalid username, email, or password.' });
}
            // Save variables to session matching your server.js parameters
            req.session.userId = user.id;
            req.session.username = user.username;
            req.session.role = user.role; // e.g., 'admin' or 'staff'

            // Save the session to database and redirect cleanly
            req.session.save((err) => {
                if (err) {
                    console.error('Session save exception:', err);
                    return res.render('auth/login', { error: 'Session creation failed.' });
                }
                res.redirect('/dashboard');
            });

        } catch (error) {
            console.error('Database auth error:', error);
            res.render('auth/login', { error: 'A server anomaly occurred. Please try again.' });
        }
    },

    // 3. Clear session store variables on leave
    handleLogout: (req, res) => {
        req.session.destroy((err) => {
            if (err) {
                console.error('Error destroying user session:', err);
            }
            res.clearCookie('session-cookie');
            res.redirect('/login');
        });
    }
};

module.exports = AuthController;