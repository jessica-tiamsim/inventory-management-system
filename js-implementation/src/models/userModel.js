const pool = require('../../config/db');

const UserModel = {
    // Looks up user rows if either the unique username OR the email column matches
    async findByUsernameOrEmail(identifier) {
        const query = `
            SELECT * FROM users 
            WHERE username = ? OR email = ? 
            LIMIT 1
        `;
        const [rows] = await pool.execute(query, [identifier, identifier]);
        return rows[0] || null;
    }
};

module.exports = UserModel;