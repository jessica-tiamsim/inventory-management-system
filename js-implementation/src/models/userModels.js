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
    }
};

module.exports = UserModel;