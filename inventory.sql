CREATE DATABASE db;
USE db;

CREATE TABLE PRODUCTS (
	user_id INT PRIMARY KEY,
    username VARCHAR(255) UNIQUE,
    email VARCHAR(255) UNIQUE,
    password_hash VARCHAR(255),
    role ENUM('admin', 'staff'),
    user_is_active BOOLEAN,
    user_created_at DATETIME,
    user_updated_at DATETIME
);
