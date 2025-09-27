-- Create database
CREATE DATABASE IF NOT EXISTS online_notes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE online_notes;

-- Users table
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(200) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Notes table
CREATE TABLE notes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  subject VARCHAR(150),
  category VARCHAR(50),
  filename VARCHAR(255) NOT NULL,
  rating FLOAT DEFAULT 0,
  rating_count INT DEFAULT 0,
  downloads INT DEFAULT 0,
  status ENUM('pending','approved','rejected') DEFAULT 'pending',
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default admin user (password = admin123, hashed)
INSERT INTO users (name,email,password,role) VALUES (
  'Administrator',
  'admin@example.com',
  '$2y$10$e0NRBq4gG9uG/2QvJZQGqe8z3h6Z/3j7a8eM2m1w3VQ7a7y1Xy1aW',
  'admin'
);
