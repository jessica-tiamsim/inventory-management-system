require('dotenv').config(); 
const express = require('express');
const path = require('path');
const session = require('express-session');
const MySQLStore = require('express-mysql-session')(session);

const app = express();
const dbPool = require('./config/db');

// Set native EJS view configurations
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

// Body parsing & Static Files Middleware
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(express.static(path.join(__dirname, 'public')));

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


app.use((req, res, next) => {
    // 1. Safely check if a session exists before reading user data
    res.locals.user = (req.session && req.session.user) ? req.session.user : null;
    
    // 2. Safely check for flash errors. If no session exists yet, default to null.
    if (req.session) {
        res.locals.error = req.session.loginError || null;
        delete req.session.loginError; // Clean up the error message
    } else {
        res.locals.error = null;
    }
    
    next();
});

// Root Routing Connections
const mainRoutes = require('./src/routes/index');
app.use('/', mainRoutes);

// Fallback Error Middleware
app.use((err, req, res, next) => {
    console.error(err.stack);
    res.status(err.status || 500).render('error', { 
        message: err.message || 'Internal Server Error' 
    });
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => console.log(`PRISM System active on port ${PORT}`));