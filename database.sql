CREATE DATABASE IF NOT EXISTS zoo_management;
USE zoo_management;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS animals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    species VARCHAR(100) NOT NULL,
    age INT,
    weight DECIMAL(10,2),
    health_status ENUM('Sağlıklı', 'Hasta', 'İyileşiyor', 'Tedavi Altında') DEFAULT 'Sağlıklı',
    feeding_schedule TEXT,
    habitat VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO animals (name, species, age, weight, health_status, feeding_schedule, habitat, notes) VALUES
('Aslan Leo', 'Aslan', 5, 180.50, 'Sağlıklı', 'Günde 2 kez et', 'Afrika Savanası', 'Çok sosyal ve ziyaretçi dostu'),
('Fil Maya', 'Afrika Fili', 12, 3500.00, 'Sağlıklı', 'Günde 3 kez sebze ve meyve', 'Fil Bahçesi', 'Zeki ve oyuncu'),
('Kaplan Şira', 'Bengal Kaplanı', 3, 220.75, 'Tedavi Altında', 'Günde 2 kez et + vitamin', 'Asya Ormanı', 'Arka bacakta hafif yara mevcut'); 