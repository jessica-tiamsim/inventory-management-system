const db = require('../../config/db');

const ProductModel = {
    getDashboardStats: async () => {
        const query = `
            SELECT 
                COUNT(DISTINCT CASE WHEN p.is_active = 1 THEN p.id END) AS activeProducts,
                COUNT(DISTINCT CASE WHEN p.is_active = 0 THEN p.id END) AS inactiveProducts,
                COALESCE(SUM(CASE 
                    WHEN m.movement_type = 'in'  THEN  m.quantity 
                    WHEN m.movement_type = 'out' THEN -m.quantity
                    WHEN m.movement_type = 'adjustment' THEN m.quantity
                    ELSE 0 
                END), 0) AS totalUnits,
                COALESCE(SUM((CASE 
                    WHEN m.movement_type = 'in'  THEN  m.quantity 
                    WHEN m.movement_type = 'out' THEN -m.quantity
                    WHEN m.movement_type = 'adjustment' THEN m.quantity
                    ELSE 0 
                END) * p.unit_cost), 0) AS inventoryValue
            FROM products p 
            LEFT JOIN stock_movements m ON p.id = m.product_id
        `;
        const [rows] = await db.execute(query);
        return rows[0]; 
    },

    getLowStockProducts: async () => {
        const query = `
            SELECT 
                p.id, p.sku, p.name, p.reorder_threshold, p.supplier_name,
                c.name AS category_name,
                COALESCE(SUM(CASE 
                    WHEN m.movement_type = 'in'         THEN  m.quantity 
                    WHEN m.movement_type = 'out'        THEN -m.quantity 
                    WHEN m.movement_type = 'adjustment' THEN  m.quantity
                    ELSE 0 
                END), 0) AS current_quantity
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN stock_movements m ON p.id = m.product_id 
            WHERE p.is_active = 1
            GROUP BY p.id, p.sku, p.name, p.reorder_threshold, p.supplier_name, c.name
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

    getFilteredProducts: async (searchTerm = '', categoryFilter = '', statusFilter = '2', limit = 10, offset = 0) => {
        try {
            let baseQuery = `
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
            `;
            
            const params = [];
            const conditions = [];

            if (searchTerm && searchTerm.trim() !== '') {
                conditions.push("(p.name LIKE ? OR p.sku LIKE ?)");
                const formattedTerm = `%${searchTerm.trim()}%`;
                params.push(formattedTerm, formattedTerm);
            }
            if (categoryFilter && categoryFilter !== '') {
                conditions.push("p.category_id = ?");
                params.push(parseInt(categoryFilter, 10));
            }
            if (statusFilter === '0' || statusFilter === '1') {
                conditions.push("p.is_active = ?");
                params.push(parseInt(statusFilter, 10));
            }

            if (conditions.length > 0) {
                baseQuery += " WHERE " + conditions.join(" AND ");
            }

            // Count total rows
            const [countRows] = await db.execute(`SELECT COUNT(*) AS total ${baseQuery}`, params);
            const total = countRows[0].total;

            // Fetch paginated rows
            const dataQuery = `
                SELECT p.id, p.sku, p.name, p.description, p.category_id, 
                       c.name as category_name, p.unit_price, p.unit_cost, 
                       p.reorder_threshold, p.supplier_name, p.is_active 
                ${baseQuery}
                ORDER BY p.id DESC
                LIMIT ${parseInt(limit)} OFFSET ${parseInt(offset)}
            `;
            const [rows] = await db.execute(dataQuery, params);
            return { rows, total };
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
    },

    updateProduct: async (id, name, description, category_id, unit_cost, unit_price, reorder_threshold, supplier_name) => {
        const query = `
            UPDATE products 
            SET name = ?, description = ?, category_id = ?, unit_cost = ?, unit_price = ?, 
                reorder_threshold = ?, supplier_name = ?, updated_at = NOW()
            WHERE id = ?
        `;
        const [result] = await db.execute(query, [name, description || null, category_id, unit_cost, unit_price, reorder_threshold, supplier_name || null, id]);
        return result;
    },

    softDeleteProduct: async (id) => {
        const query = `UPDATE products SET is_active = 0, updated_at = NOW() WHERE id = ?`;
        const [result] = await db.execute(query, [id]);
        return result;
    }
};

module.exports = ProductModel;