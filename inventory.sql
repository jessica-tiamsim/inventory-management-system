CREATE DATABASE db;
USE db;

CREATE TABLE PRODUCTS (
	user_id INT,
    username VARCHAR(255),
    email VARCHAR(255),
    password_hash VARCHAR(255),
    role ENUM('Admin', 'Staff'),
    user_is_active BOOLEAN,
    user_created_at DATETIME,
    user_updated_at DATETIME
);