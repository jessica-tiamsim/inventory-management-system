const db = require('../../config/db'); // Points to promise-based mysql2 pool

const UserModel = {
    findByUsernameOrEmail: async (identifier) => {
        const query = `
            SELECT id, username, email, password_hash, role 
            FROM users 
            WHERE username = ? OR email = ? LIMIT 1
        `;
        const [rows] = await db.execute(query, [identifier, identifier]);
        return rows[0]; // Returns user layout record or undefined
    },

getAllUsers: async () => {
        const query = 'SELECT id, username, email, role FROM users ORDER BY id DESC';
        const [rows] = await db.execute(query);
        return rows;
    },

    // 3. Processes matching conditions submitted through your search form input
    searchUsersByUsername: async (term) => {
        const query = 'SELECT id, username, email, role FROM users WHERE username LIKE ? ORDER BY id DESC';
        const [rows] = await db.execute(query, [`%${term}%`]);
        return rows;
    },

    // 4. Writes registration payloads into your backend database
    createUser: async (username, email, passwordHash, role) => {
        const query = 'INSERT INTO users (username, email, password_hash, role, status) VALUES (?, ?, ?, ?, "active")';
        const [result] = await db.execute(query, [username, email, passwordHash, role || 'staff']);
        return result;
    }
};

module.exports = UserModel;