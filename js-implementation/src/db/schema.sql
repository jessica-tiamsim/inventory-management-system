CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'staff',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE, -- Must be UNIQUE per handout
    description TEXT
);

-- 3. Create the Products Table exactly as required
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Standard auto-incrementing ID
    sku VARCHAR(255) NOT NULL UNIQUE,  -- Must be UNIQUE per handout
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category_id INT,
    unit_price DECIMAL(10, 2) DEFAULT 0.00,
    unit_cost DECIMAL(10, 2) DEFAULT 0.00,
    reorder_threshold INT DEFAULT 0,
    supplier_name VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,    -- Maps to BOOLEAN
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Create the official table matching Page 12 guidelines
CREATE TABLE stock_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT, -- Must be INT to match products.id
    user_id INT,    -- Must be INT to match users.id
    movement_type ENUM('in', 'out', 'adjustment') NOT NULL, -- Fixed 'adjustment' name
    quantity INT NOT NULL, -- Standard INT is signed by default in MySQL (allows negative/positive)
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   
    -- Relationships configuration
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

