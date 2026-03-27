-- SQL to create the users table
-- Run this in your phpMyAdmin or MySQL client

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_active` TINYINT DEFAULT 1,
  INDEX `idx_username` (`username`),
  INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional: Create an admin user (password: Admin@12345)
-- INSERT INTO users (username, email, password) VALUES 
-- ('admin', 'admin@example.com', '$2y$10$YOixf7lW0Ft/gwWMmA7pPO88qzSIkYWnBfBxHVJr0G0xYLz.VBhya');
