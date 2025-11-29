-- schema.sql

CREATE DATABASE IF NOT EXISTS rental_broker DEFAULT CHARACTER SET utf8mb4;
USE rental_broker;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(32) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('guest','renter','owner','admin') NOT NULL DEFAULT 'renter',
    created_at DATETIME NOT NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active'
);

-- Properties Table
CREATE TABLE properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT NOT NULL,
    title VARCHAR(120) NOT NULL,
    location VARCHAR(120) NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    type ENUM('apartment','house','condo') NOT NULL,
    description TEXT NOT NULL,
    images TEXT,
    availability ENUM('available','not available') NOT NULL DEFAULT 'available',
    created_at DATETIME NOT NULL,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Favorites Table
CREATE TABLE favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    property_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
);

-- Inquiries Table
CREATE TABLE inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    sender_id INT NOT NULL,
    owner_id INT NOT NULL,
    message TEXT NOT NULL,
    status ENUM('pending','approved','declined') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Logs Table
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event VARCHAR(255) NOT NULL,
    user_id INT,
    details TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
