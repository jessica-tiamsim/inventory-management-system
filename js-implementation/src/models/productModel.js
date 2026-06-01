const db = require('../../config/db');

const ProductModel = {
    getDashboardStats: async () => {
        const query = `
            SELECT 
                COUNT(DISTINCT CASE WHEN p.is_active = 1 THEN p.id END) as activeProducts,
                COUNT(DISTINCT CASE WHEN p.is_active = 0 THEN p.id END) as inactiveProducts,
                COALESCE(SUM(CASE 
                    WHEN m.movement_type = 'in' THEN m.quantity 
                    WHEN m.movement_type IN ('out', 'adjustment') THEN -m.quantity 
                    ELSE 0 
                END), 0) as totalUnits,
                COALESCE(SUM((CASE 
                    WHEN m.movement_type = 'in' THEN m.quantity 
                    WHEN m.movement_type IN ('out', 'adjustment') THEN -m.quantity 
                    ELSE 0 
                END) * p.unit_cost), 0) as inventoryValue
            FROM products p 
            LEFT JOIN stock_movements m ON p.id = m.product_id
        `;
        const [rows] = await db.execute(query);
        return rows[0]; 
    },

    getLowStockProducts: async () => {
        const query = `
            SELECT p.name, COALESCE(SUM(CASE 
                WHEN m.movement_type = 'in' THEN m.quantity 
                WHEN m.movement_type IN ('out', 'adjustment') THEN -m.quantity 
                ELSE 0 
            END), 0) as current_quantity
            FROM products p 
            LEFT JOIN stock_movements m ON p.id = m.product_id 
            WHERE p.is_active = 1
            GROUP BY p.id, p.name, p.reorder_threshold 
            HAVING current_quantity <= p.reorder_threshold 
            ORDER BY current_quantity ASC 
            LIMIT 5
        `;
        const [rows] = await db.execute(query);
        return rows; 
    },

    getAllProducts: async () => {
        const query = `
            SELECT p.id, p.sku, p.name, p.description, p.category_id, 
                   c.name as category_name, p.unit_price, p.unit_cost, 
                   p.reorder_threshold, p.supplier_name, p.is_active 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY p.id DESC
        `;
        const [rows] = await db.execute(query);
        return rows;
    },

    searchProducts: async (term) => {
        const query = `
            SELECT p.id, p.sku, p.name, p.description, p.category_id, 
                   c.name as category_name, p.unit_price, p.unit_cost, 
                   p.reorder_threshold, p.supplier_name, p.is_active 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.name LIKE ? OR p.sku LIKE ?
            ORDER BY p.id DESC
        `;
        const formattedTerm = `%${term}%`;
        const [rows] = await db.execute(query, [formattedTerm, formattedTerm]);
        return rows;
    },

    getFilteredProducts: async (searchTerm = '', productsFilter = 'all') => {
        try {
            let query = `
                SELECT p.id, p.sku, p.name, p.description, p.category_id, 
                       c.name as category_name, p.unit_price, p.unit_cost, 
                       p.reorder_threshold, p.supplier_name, p.is_active 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
            `;
            
            const params = [];
            const conditions = [];

            // 1. Handle Text Search Input Bar (Matches Name or SKU)
            if (searchTerm && searchTerm.trim() !== '') {
                conditions.push("(p.name LIKE ? OR p.sku LIKE ?)");
                const formattedTerm = `%${searchTerm.trim()}%`;
                params.push(formattedTerm, formattedTerm);
            }

            // 2. Handle Individual Product Dropdown Filter Condition
            if (productsFilter && productsFilter !== 'all') {
                conditions.push("p.id = ?");
                params.push(parseInt(productsFilter, 10));
            }

            if (conditions.length > 0) {
                query += " WHERE " + conditions.join(" AND ");
            }

            query += " ORDER BY p.id DESC";

            const [rows] = await db.execute(query, params);
            return rows;
        } catch (error) {
            console.error("Database query exception inside ProductModel.getFilteredProducts:", error);
            throw error;
        }
    },
    
    createProduct: async (sku, name, description, category_id, unit_cost, unit_price, reorder_threshold, supplier_name) => {
        const query = `
            INSERT INTO products (sku, name, description, category_id, unit_cost, unit_price, reorder_threshold, supplier_name, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
        `;
        
        const baseCategoryId = (category_id && category_id !== '' && category_id !== '#' && !isNaN(category_id)) 
            ? parseInt(category_id, 10) 
            : null;
        
        const [result] = await db.execute(query, [
            sku, 
            name, 
            description || null, 
            baseCategoryId, 
            unit_cost, 
            unit_price, 
            reorder_threshold, 
            supplier_name || null
        ]);
        return result;
    },

    getAllActiveProducts: async () => {
        const query = `SELECT id, name, sku FROM products WHERE is_active = 1 ORDER BY name ASC`;
        const [rows] = await db.execute(query);
        return rows;
    },

    getAllCategories: async () => {
        const query = `SELECT id, name FROM categories ORDER BY name ASC`;
        const [rows] = await db.execute(query);
        return rows;
    }
};

module.exports = ProductModel;