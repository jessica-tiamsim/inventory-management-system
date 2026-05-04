CREATE DATABASE db;
USE db;

CREATE TABLE PRODUCTS (
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
	id VARCHAR(255),
    name VARCHAR(255) UNIQUE,
    description TEXT
);