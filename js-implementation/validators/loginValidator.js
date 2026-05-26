const Joi = require('joi');

const authValidator = Joi.object({ // Kept variable name
    username_email: Joi.string().trim().min(3).required().messages({
        'string.empty': 'Username or Email field cannot be empty.',
        'any.required': 'Username or Email is required.'
    }),
    password: Joi.string().min(6).required().messages({
        'string.empty': 'Password field cannot be empty.',
        'string.min': 'Password must satisfy minimum security configurations.'
    })
});

module.exports = { authValidator };