// src/validators/loginValidator.js
const Joi = require('joi');

const authValidator = Joi.object({
    username_email: Joi.string().required().messages({
        'string.empty': 'Please enter both username/email and password.',
        'any.required': 'Please enter both username/email and password.'
    }),
    
    password: Joi.string().required().messages({
        'string.empty': 'Please enter both username/email and password.',
        'string.min': 'Password must be at least 6 characters long.',
        'any.required': 'Please enter both username/email and password.'
    })
});

module.exports = { authValidator };