-- ==========================================
-- PRISM INITIALIZATION AND SAFETY UTILITIES
-- ==========================================
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS stock_movements;
DROP TABLE IF EXISTS sessions_table;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- ==========================================
-- 1. BASE INDEPENDENT SCHEMAS
-- ==========================================

-- System Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'staff',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Product Categories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT
);

-- System Sessions
CREATE TABLE sessions_table (
    session_id VARCHAR(255) NOT NULL,
    user_id INT NOT NULL,
    expires_at INT NOT NULL,
    PRIMARY KEY (session_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ==========================================
-- 2. DEPENDENT CHILD SCHEMAS
-- ==========================================

-- Products Catalog (Depends on Categories)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category_id INT,
    unit_price DECIMAL(10, 2) DEFAULT 0.00,
    unit_cost DECIMAL(10, 2) DEFAULT 0.00,
    reorder_threshold INT DEFAULT 0,
    supplier_name VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Stock Movements Ledger (Depends on Products and Users)
CREATE TABLE stock_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT,
    movement_type ENUM('in', 'out', 'adjustment') NOT NULL,
    quantity INT NOT NULL,
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);


-- ==========================================
-- 3. CORE SYSTEM DATA SEEDING
-- ==========================================

-- Seed Accounts (Password format: password + username)
INSERT INTO users (id, username, email, password_hash, role, is_active) VALUES
(1, 'jc', 'jc@prism.com', '$2y$10$ez.1/kALBqRyb.vy9qzBCuu77xuwKdvKwuoTYLifBOfUPkMOKEoiS', 'admin', 1), 
(2, 'wren', 'wren@prism.com', '$2y$10$ApxFhQQtj1XiSkJhdFs1z.a7zTBboEfuy1A8ph1HOR8Lq9.5ARD9a', 'staff', 1), 
(3, 'erl', 'erl@prism.com', '$2y$10$5cXTVUDqd9DQkpXmxpeIQeG83NXjMbnkY9QY2VjmxPep49Bo1N1IO', 'staff', 1), 
(4, 'ymir', 'ymir@prism.com', '$2y$10$21JvSylWhJbRfW71yOD0y.7zjkMrqiI0uDJ0kxmaLLTqqK1Tzm3QK', 'staff', 1),
(5, 'renzo', 'renzo@prism.com', '$2y$10$r7Vr/OhCbjJS5aAKgZZbuOOHtTnKIR.PyCrvYKTvUdrGxLPSZGd7y', 'admin', 1), 
(6, 'mik', 'mik@prism.com', '$2y$10$J6rpABEL9rRLQfsp9HCSQe3Fv8Tbg9AM1CRkCYwOQuBlJZ51H4VVW', 'admin', 1), 
(7, 'dayle', 'dayle@prism.com', '$2y$10$g26Grt5otzn4.w5EglnyNOoZyaS/GV2BSTlGZp3tzLu8y2SZ0RwsK', 'admin', 1);

-- Seed Categories
INSERT INTO categories (id, name, description) VALUES
(1, 'Meat', 'Fresh and processed poultry, beef, pork, and lamb cuts.'),
(2, 'Seafood', 'Freshwater and saltwater fish, shellfish, and crustacean items.'),
(3, 'Vegetables', 'Fresh leafy greens, root vegetables, and seasonal produce.'),
(4, 'Fruits', 'Fresh, dried, and preserved fruits and citrus varieties.'),
(5, 'Dairy Products', 'Milk, cheese, butter, yogurt, and other cream-based items.'),
(6, 'Dry Goods', 'Shelf-stable pantry essentials including grains, pasta, flour, and beans.'),
(7, 'Beverages', 'Juices, sodas, bottled water, coffee, tea, and alcoholic drinks.'),
(8, 'Frozen Goods', 'Pre-packaged meals, ice cream, and flash-frozen ingredients.'),
(9, 'Condiments & Sauces', 'Table sauces, dressings, vinegar, oils, and spreads.'),
(10, 'Spice & Seasonings', 'Dried herbs, ground spices, baking seasonings, and salt blends.');

-- Seed Products
INSERT INTO products (sku, name, description, category_id, unit_price, unit_cost, reorder_threshold, supplier_name, is_active) VALUES
('MEAT-001', 'Ribeye Steak', 'Premium choice boneless beef ribeye cuts', 1, 18.99, 11.50, 15, 'Prime Cuts Wholesale', 1),
('MEAT-002', 'Chicken Breast', 'Fresh skinless, boneless chicken breasts (5kg)', 1, 24.50, 14.00, 20, 'Valley Poultry Farm', 1),
('SEAF-001', 'Atlantic Salmon Fillet', 'Freshly caught Atlantic salmon, skin-on', 2, 22.00, 13.20, 10, 'Ocean Fresh Distributors', 1),
('SEAF-002', 'Tiger Prawns', 'Frozen whole jumbo tiger prawns (1kg bag)', 2, 16.99, 9.50, 12, 'Ocean Fresh Distributors', 1),
('VEGE-001', 'Organic Spinach', 'Pre-washed baby spinach leaves (500g)', 3, 4.25, 1.80, 30, 'Green Earth Organics', 1),
('VEGE-002', 'Russet Potatoes', 'Bulk 10lb bag of baking potatoes', 3, 6.99, 2.50, 25, 'Green Earth Organics', 1),
('FRUT-001', 'Fuji Apples', 'Crisp sweet Fuji apples (per kg)', 4, 3.50, 1.20, 40, 'Sun-Rays Fruit Co.', 1),
('FRUT-002', 'Cavendish Bananas', 'Fresh yellow bananas bunch', 4, 1.99, 0.65, 50, 'Sun-Rays Fruit Co.', 1),
('DAIR-001', 'Whole Milk 1G', 'Pasteurized vitamin D whole milk', 5, 4.19, 1.90, 35, 'Cloverland Dairy', 1),
('DAIR-002', 'Cheddar Cheese Block', 'Sharp white cheddar block (1kg)', 5, 11.50, 5.20, 15, 'Cloverland Dairy', 1),
('DRYG-001', 'Jasmine Rice', 'Long grain premium Thai jasmine rice (5kg)', 6, 12.99, 6.00, 20, 'Global Grain Traders', 1),
('DRYG-002', 'Spaghetti Pasta', 'Semolina wheat spaghetti noodles (1kg pack)', 6, 2.49, 0.95, 60, 'Global Grain Traders', 1),
('BEVE-001', 'Pure Coconut Water', '100% Organic coconut water (1L pack)', 7, 3.99, 1.75, 45, 'AquaVibe Drinks', 1),
('BEVE-002', 'Roasted Coffee Beans', 'Medium roast arabica coffee beans (1kg)', 7, 18.50, 8.20, 15, 'Apex Roasters', 1),
('FROZ-001', 'Pepperoni Pizza', 'Thin crust stone-baked frozen pizza', 8, 7.99, 3.40, 25, 'IceBox Foods', 1),
('FROZ-002', 'Vanilla Ice Cream', 'Premium double bean vanilla ice cream (2L)', 8, 5.49, 2.10, 20, 'IceBox Foods', 1),
('COND-001', 'Extra Virgin Olive Oil', 'Cold-pressed extra virgin olive oil (750ml)', 9, 14.99, 7.10, 15, 'Mediterranean Import Co.', 1),
('COND-002', 'Organic Ketchup', 'Tomato ketchup made with cane sugar', 9, 3.89, 1.45, 40, 'Heinz Distributors', 1),
('SPIC-001', 'Sea Salt Coarse', 'Natural iodized coarse sea salt (1kg)', 10, 2.99, 0.85, 30, 'SpiceRoute Wholesalers', 1),
('SPIC-002', 'Black Pepper Ground', 'Pure ground black peppercorns (500g)', 10, 8.50, 3.20, 20, 'SpiceRoute Wholesalers', 1);

-- Seed Stock Ledger
INSERT INTO stock_movements (product_id, user_id, movement_type, quantity, note) VALUES
(1, 1, 'in', 40, 'Received initial batch of Ribeye steaks'),
(2, 2, 'in', 60, 'Chicken breasts loaded into cold storage room A'),
(3, 3, 'in', 25, 'Atlantic Salmon shipment accepted and inspected'),
(4, 4, 'in', 35, 'Tiger prawns added to freezer shelf 1'),
(5, 5, 'in', 80, 'Spinach cases unboxed and checked for freshness'),
(6, 6, 'in', 50, 'Russet potato bags stacked on pallets'),
(7, 7, 'in', 100, 'Fuji apples intake logged for produce section'),
(8, 1, 'in', 120, 'Bananas received green; moved to ripening room'),
(9, 2, 'in', 70, 'Whole milk delivery stocked directly to display'),
(10, 3, 'in', 45, 'Cheddar cheese wheel stock added'),
(11, 4, 'in', 50, 'Jasmine rice pallets stored in dry dry room B'),
(12, 5, 'in', 200, 'Bulk spaghetti packs inventoried'),
(13, 6, 'in', 150, 'Coconut water cartons stacked in aisle 4'),
(14, 7, 'in', 40, 'Premium coffee bags stored away from moisture'),
(15, 1, 'in', 60, 'Frozen pizzas transferred directly to display freezers'),
(16, 2, 'in', 55, 'Ice cream tubs stocked in walk-in freezer'),
(17, 3, 'in', 30, 'Olive oil cases unboxed'),
(18, 4, 'in', 120, 'Ketchup bottles organized on condiment shelves'),
(19, 5, 'in', 100, 'Sea salt canisters checked into dry inventory'),
(20, 6, 'in', 50, 'Ground black pepper bulk jars shelved');



