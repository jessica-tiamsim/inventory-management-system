const express = require('express');
const session = require('express-session')
const MySQLStore = require('express-mysql-session')(session);
const bcrypt = require('bcrypt');
const path = require('path');
require('dotenv').config();

const db = require('./db/connection');
const app = express();

app.use(express.json());
app.use(express.static(path.join(__dirname, '../public')));

/* SESSION STORE (MySQL) */
const sessionStore = new MySQLStore({
    host:process.env.DB_HOST,
    user:process.env.DB_USER,
    password:process.env.DB_PASSWORD,
    database:process.env.DB_NAME,
});

app.use(session({
    key:'session_cookie',
    secret:process.env.SESSION_SECRET,
    store:sessionStore,
    resave:false,
    saveUninitialized: false,
    cookie: {
        maxAge: 1000 * 60 * 60 // 1hour
    }
}));


/*LOGIN*/
app.post('/login', (req, res) => {

  const { username, password } = req.body;

  console.log("BODY RECEIVED:", req.body);

  if (!username || !password) {
    return res.json({ message: 'Missing fields' });
  }

  db.query(
    'SELECT * FROM users WHERE username = ? OR email = ?',
    [username, username],
    async (err, results) => {

      if (err) {
        return res.status(500).json({ message: 'Database error' });
      }

      if (results.length === 0) {
        return res.json({ message: 'User not found' });
      }

      const user = results[0];

      // ✅ IMPORTANT: use user_pass here
      if (!user.password_hash) {
        return res.json({ message: 'Invalid user data' });
      }

      try {
        const match = await bcrypt.compare(password, user.password_hash);

        if (!match) {
          return res.json({ message: 'Wrong password' });
        }

        req.session.user = {
          id: user.id,
          username: user.username
        };

        return res.json({ message: 'Login successful' });

      } catch (e) {
        return res.status(500).json({ message: 'Server error' });
      }
    });
});

/* DASHBOARD */
app.get('/dashboard', (req, res) => {
    if (!req.session.user) {
        return res.status(401).json({message: 'Not logged in'});
    }
    res.send({message:`Welcome ${req.session.user.username}!` });
});

app.get('/logout', (req, res) => {
    req.session.destroy();
    res.json({message:'Logged Out'});
});

app.listen(process.env.PORT, () => {
    console.log('Server Running');
});