const db = require('../../config/db'); // Points to your promise-based database pool

const ProfileModel = {
    /**
     * Fetches all non-deactivated profiles to populate the active user registry layout
     */
    getAllUsers: async () => {
        const query = `
            SELECT id, username, email, role 
            FROM users 
            ORDER BY id DESC
        `;
        const [rows] = await db.execute(query);
        return rows;
    },

    /**
     * Traverses users matching fuzzy search string parameters from the filter bar
     * @param {string} term - The user input string targeted against the username column
     */
    searchUsersByUsername: async (term) => {
        const query = `
            SELECT id, username, email, role 
            FROM users 
            WHERE username LIKE ?
            ORDER BY id DESC
        `;
        const [rows] = await db.execute(query, [`%${term}%`]);
        return rows;
    },

    /**
     * Submits and writes parameters down to the database infrastructure
     */
    createUser: async (username, email, passwordHash, role) => {
        const query = `
            INSERT INTO users (username, email, password_hash, role) 
            VALUES (?, ?, ?, ?)
        `;
        const [result] = await db.execute(query, [username, email, passwordHash, role || 'staff']);
        return result;
    }
};

module.exports = ProfileModel;