const express = require('express');
const path = require("path");
const session = require('express-session');
const MySQLStore = require('express-mysql-session')(session);
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 3000;
const pool = require('./config/db');

// 👇 FIXED: Import your controller so Express can route requests to it
const AuthController = require('./src/controllers/authController');

// EJS SETUP //
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

// Middlewares
app.use(express.urlencoded({ extended: true }));
app.use(express.static(path.join(__dirname, 'public')));

// Persistent MySQL Session Setup
const sessionStore = new MySQLStore({}, pool);
app.use(session({
    key: 'session-cookie',
    secret: process.env.SESSION_SECRET || 'your_fallback_unbreakable_secret',
    store: sessionStore, // 👇 FIXED: Changed 'STORE' to lowercase 'store'
    resave: false,
    saveUninitialized: false,
    cookie: {
        maxAge: 1000 * 60 * 60 * 12, // 12 Hours
        httpOnly: true
    }
}));

// Auth Routes
app.get('/login', AuthController.showLoginForm);
app.post('/login', AuthController.handleLogin);
app.get('/logout', AuthController.handleLogout); // 👇 FIXED: Changed '/login' to '/logout'

// Dashboard Route
app.get('/dashboard', async (req, res) => { // 👇 FIXED: Added missing leading slash '/'
    // 👇 FIXED: Changed 'userID' to 'userId' to match your controller session payload
    if (!req.session.userId) {
        return res.redirect('/login');
    }
    res.send(`
        <h1>Dashboard Mock Entry Point</h1>
        <p>Active Session: <strong>${req.session.username}</strong> (${req.session.role})</p>
        <a href="/logout">Logout</a>
    `);
});

app.listen(PORT, () => {
    console.log(`Development MVP server online at http://localhost:${PORT}/login`);
});