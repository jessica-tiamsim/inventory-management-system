const express = require('express');
const path = require('path');
const session = require('express-session');
const MySQLStore = require('express-mysql-session')(session);
require('dotenv').config();

const app = express();
const dbPool = require('./config/db'); // Points to the file you just made

// Set native EJS view configurations
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

// Body parsing & Static Files Middleware
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(express.static(path.join(__dirname, 'public')));
app.use('/assets', express.static(path.join(__dirname, '../assets')));

// Configure Session Store mapping using MySQL Pool
const sessionStore = new MySQLStore({}, dbPool);
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

// --- ROUTING ---

const mainRoutes = require('./src/routes/index');
app.use('/', mainRoutes);

// Temporary Test Route
app.get('/', (req, res) => {
    res.send("<h2>PRISM Server is alive! Database connected. Ready to build routes.</h2>");
});

// Fallback Error Middleware
app.use((err, req, res, next) => {
    console.error(err.stack);
    res.status(err.status || 500).send('Internal Server Error');
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => console.log(`PRISM System active on port ${PORT}`));