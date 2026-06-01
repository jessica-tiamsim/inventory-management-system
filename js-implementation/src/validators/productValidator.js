// src/validators/productValidator.js
const Joi = require('joi');

const createProductValidator = Joi.object({
    sku: Joi.string().trim().max(255).required().messages({
        'string.empty': 'SKU is required.',
        'any.required': 'SKU is required.',
        'string.max': 'SKU must be 255 characters or fewer.'
    }),
    name: Joi.string().trim().max(255).required().messages({
        'string.empty': 'Product name is required.',
        'any.required': 'Product name is required.',
        'string.max': 'Product name must be 255 characters or fewer.'
    }),
    description: Joi.string().trim().max(1000).allow('', null).optional(),
    category_id: Joi.alternatives()
        .try(Joi.number().integer().positive(), Joi.string().allow('', null))
        .optional(),
    unit_cost: Joi.number().min(0).required().messages({
        'number.base': 'Unit cost must be a valid number.',
        'number.min': 'Unit cost cannot be negative.',
        'any.required': 'Unit cost is required.'
    }),
    unit_price: Joi.number().min(0).required().messages({
        'number.base': 'Unit price must be a valid number.',
        'number.min': 'Unit price cannot be negative.',
        'any.required': 'Unit price is required.'
    }),
    reorder_threshold: Joi.number().integer().min(0).required().messages({
        'number.base': 'Reorder threshold must be a valid number.',
        'number.integer': 'Reorder threshold must be a whole number.',
        'number.min': 'Reorder threshold cannot be negative.',
        'any.required': 'Reorder threshold is required.'
    }),
    supplier_name: Joi.string().trim().max(255).allow('', null).optional()
});

const editProductValidator = Joi.object({
    id: Joi.number().integer().positive().required().messages({
        'any.required': 'Product ID is required for editing.'
    }),
    name: Joi.string().trim().max(255).required().messages({
        'string.empty': 'Product name is required.',
        'any.required': 'Product name is required.'
    }),
    description: Joi.string().trim().max(1000).allow('', null).optional(),
    category_id: Joi.alternatives()
        .try(Joi.number().integer().positive(), Joi.string().allow('', null))
        .optional(),
    unit_cost: Joi.number().min(0).required().messages({
        'number.base': 'Unit cost must be a valid number.',
        'number.min': 'Unit cost cannot be negative.'
    }),
    unit_price: Joi.number().min(0).required().messages({
        'number.base': 'Unit price must be a valid number.',
        'number.min': 'Unit price cannot be negative.'
    }),
    reorder_threshold: Joi.number().integer().min(0).required().messages({
        'number.base': 'Reorder threshold must be a valid number.',
        'number.integer': 'Reorder threshold must be a whole number.',
        'number.min': 'Reorder threshold cannot be negative.'
    }),
    supplier_name: Joi.string().trim().max(255).allow('', null).optional()
});

module.exports = { createProductValidator, editProductValidator };
