const express = require('express');
const path = require('path');
const session = require('express-session');
const MySQLStore = require('express-mysql-session')(session);
const cookieParser = require('cookie-parser');
require('dotenv').config();

const app = express();
const dbPool = require('./config/db'); // Points to the file you just made

// Set native EJS view configurations
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

// Body parsing & Static Files Middleware
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(cookieParser());
app.use(express.static(path.join(__dirname, 'public')));

// Configure Session Store mapping using MySQL Pool
const sessionStore = new MySQLStore({
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_NAME
});

app.use(session({
    key: 'prism_session',
    secret: process.env.SESSION_SECRET || 'fallback-secret',
    store: sessionStore,
    resave: false,
    saveUninitialized: false,
    cookie: { maxAge: 1000 * 60 * 60 * 8 } // 8 hours
}));

// Global Variable Middleware (Passes session data to EJS views)
app.use((req, res, next) => {
    res.locals.user = (req.session && req.session.user) ? req.session.user : null;
    if (req.session) {
        res.locals.error = req.session.loginError || null;
        delete req.session.loginError; 
    } else {
        res.locals.error = null;
    }
    next();
});

app.use((req, res, next) => {
    res.header('Cache-Control', 'private, no-cache, no-store, must-revalidate, max-stale=0, post-check=0, pre-check=0');
    res.header('Expires', '-1');
    res.header('Pragma', 'no-cache');
    next();
});

// --- ROUTING ---
const mainRoutes = require('./src/routes/index');
app.use('/', mainRoutes);

// Dashboard Route
app.get('/dashboard', (req, res) => {
    res.render('dashboard', { 
        user: req.user, 
        current: 'dashboard' 
    });
});

// Products Route
app.get('/products', (req, res) => {
    res.render('products', { 
        user: req.user, 
        current: 'products' 
    });
});

// Stock Movements Route
app.get('/stock_movement', (req, res) => {
    res.render('stock_movement', { 
        user: req.user, 
        current: 'stock_movement' 
    });
});

// Reports Route
app.get('/reports/low_stock', (req, res) => {
    res.render('low_stock', { 
        user: req.user, 
        current: 'reports' 
    });
});

// Profile Route
app.get('/profile', (req, res) => {
    res.render('profile', { 
        user: req.user, 
        current: 'profile' 
    });
});

app.post('/logout', (req, res) => {
    if (req.session) {
        req.session.destroy();
    } 
    res.redirect('/login'); 
});

// Fallback Error Middleware
app.use((err, req, res, next) => {
    console.error(err.stack);
    res.status(err.status || 500).send('Internal Server Error');
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => console.log(`PRISM System active on port ${PORT}`));