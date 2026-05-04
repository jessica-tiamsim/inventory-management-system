CREATE DATABASE db;
USE db;

CREATE TABLE USERS (
	id INT PRIMARY KEY,
    username VARCHAR(255) UNIQUE,
    email VARCHAR(255) UNIQUE,
    password_hash VARCHAR(255),
    role ENUM('admin', 'staff'),
    is_active BOOLEAN,
    created_at DATETIME,
    updated_at DATETIME
);

CREATE TABLE CATEGORIES (
	id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) UNIQUE,
    description TEXT
);

CREATE TABLE PRODUCTS (
	id VARCHAR(255) PRIMARY KEY,
    sku VARCHAR(255) UNIQUE,
    name VARCHAR(255),
    description TEXT,
    category_id VARCHAR(255),
    unit_price DECIMAL(10, 2),
    unit_cost DECIMAL(10, 2),
    reorder_threshold INT,
    supplier_name VARCHAR(255),
    is_active BOOLEAN,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (category_id) REFERENCES CATEGORIES(id)
);

CREATE TABLE STOCK_MOVEMENTS (
	id VARCHAR(255) PRIMARY KEY,
    product_id VARCHAR(255),
    user_id INT,
    movement_type ENUM('in', 'out', 'adjustment'),
    quality INT,
    note TEXT,
    created_at DATETIME,
    FOREIGN KEY (product_id) REFERENCES PRODUCTS(id),
    FOREIGN KEY (user_id) REFERENCES USERS(id)
);

CREATE TABLE SESSIONS (
	id VARCHAR(255) PRIMARY KEY,
    user_id INT,
    expires_at DATETIME,
    data TEXT,
    FOREIGN KEY (user_id) REFERENCES USERS(id)
);