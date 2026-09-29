-- =============================================
-- PHP CRUD Application - Database Setup Script
-- =============================================
-- This script creates the database and products table.
-- Run this in your MySQL/MariaDB server before using the app.
-- =============================================

-- Create the database (if it doesn't exist)
CREATE DATABASE IF NOT EXISTS `crud_products_db`
DEFAULT CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Select the database
USE `crud_products_db`;

-- =============================================
-- Products Table
-- =============================================
-- Stores product information including name, price, and quantity.
-- =============================================

CREATE TABLE IF NOT EXISTS `products` (
    `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary key, auto-increment',
    `name` VARCHAR(255) NOT NULL COMMENT 'Product name',
    `price` DECIMAL(10,2) NOT NULL COMMENT 'Product price',
    `quantity` INT(11) NOT NULL DEFAULT 0 COMMENT 'Product quantity in stock',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP COMMENT 'Record last update timestamp',
    PRIMARY KEY (`id`),
    KEY `idx_name` (`name`)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;
