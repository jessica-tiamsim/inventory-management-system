const profileModel = require('../models/profileModel');
const bcrypt = require('bcryptjs'); 

const profile_controller = { 
    /**
     * Compiles, filters, and loads the user list administration management matrix panel
     */
    getProfile: async (req, res) => {
        try {
            const searchTerms = req.query.search || '';
            let results;

            if (searchTerms) {
                results = await profileModel.searchUsersByUsername(searchTerms);
            } else {
                results = await profileModel.getAllUsers();
            }

            // Render profile template and supply local data matrix objects
            res.render('profile', { 
                employees: results,
                currentSearch: searchTerms
            });
        } catch (err) {
            console.error('Failed compiling employee profiles grid dataset:', err);
            res.status(500).send("Internal Server Error rendering profile panel.");
        }
    },

    /**
     * Secure account compilation that performs salt distribution prior to insertion writes
     */
    postCreateUser: async (req, res) => {
        try {
            const { username, email, password, role } = req.body;

            // Simple data integrity protection block
            if (!username || !email || !password) {
                return res.status(400).send("Bad Request: Missing required creation parameters.");
            }

            // Generate secure rounds and hash plaintext fields
            const salt = await bcrypt.genSalt(10);
            const hashedPasswordHash = await bcrypt.hash(password, salt);

            // Write row to database storage matrix
            await profileModel.createUser(username, email, hashedPasswordHash, role);
            
            // Redirect cleanly back to the profiles panel dashboard root path
            res.redirect('/profile'); 

        } catch (err) {
            console.error('Account execution exception dropped:', err);
            res.status(500).send("Database failure writing employee logs.");
        }
    }
};

// Exporting using explicit lowercase naming structure to match file system calls perfectly
module.exports = profile_controller;