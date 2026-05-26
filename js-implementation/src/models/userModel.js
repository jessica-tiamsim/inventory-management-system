const db = require('../../config/db');

exports.findByCredentials = async (identifier) => {
    const query = 'SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1';
    const [rows] = await db.execute(query, [identifier, identifier]);
    return rows[0];
};