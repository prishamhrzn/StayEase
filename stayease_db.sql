-- ============================================================
-- StayEase Hotel Booking System - Database Setup Script
-- Run this in phpMyAdmin or MySQL CLI before starting the app
-- ============================================================

CREATE DATABASE IF NOT EXISTS stayease_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE stayease_db;

-- ----------------------
-- Table: users
-- ----------------------
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    role        ENUM('user','admin') DEFAULT 'user',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------
-- Table: hotels
-- ----------------------
CREATE TABLE IF NOT EXISTS hotels (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(150)  NOT NULL,
    location    VARCHAR(200)  NOT NULL,
    price       DECIMAL(10,2) NOT NULL,
    rating      DECIMAL(2,1)  DEFAULT 0.0,
    description TEXT,
    amenities   TEXT,
    image       VARCHAR(255)  DEFAULT 'default.jpg',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------
-- Table: bookings
-- ----------------------
CREATE TABLE IF NOT EXISTS bookings (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT           NOT NULL,
    hotel_id    INT           NOT NULL,
    check_in    DATE          NOT NULL,
    check_out   DATE          NOT NULL,
    guests      INT           NOT NULL DEFAULT 1,
    total_price DECIMAL(10,2),
    status      ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)  REFERENCES users(id)  ON DELETE CASCADE,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------
-- Seed: Default Admin User
-- Password: Admin@123 (bcrypt hashed)
-- ----------------------
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@stayease.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- ----------------------
-- Seed: Sample Hotels
-- ----------------------
INSERT INTO hotels (name, location, price, rating, description, amenities, image) VALUES
('The Grand Himalayan', 'Kathmandu, Nepal', 4500.00, 4.8,
 'A luxury 5-star property with panoramic mountain views, world-class dining, and impeccable service in the heart of Kathmandu.',
 'Free WiFi,Swimming Pool,Spa,Gym,Restaurant,Bar,Room Service,Parking,Airport Shuttle',
 'grand_himalayan.jpg'),

('Azure Shores Resort', 'Pokhara, Nepal', 3200.00, 4.6,
 'Nestled on the shores of Phewa Lake, this serene resort offers stunning lake and mountain views with premium amenities.',
 'Free WiFi,Lake View,Kayaking,Restaurant,Bar,Spa,Cycling,Yoga',
 'azure_shores.jpg'),

('Heritage Courtyard Inn', 'Bhaktapur, Nepal', 1800.00, 4.4,
 'A charming boutique hotel set in a restored Newari palace in the heart of the UNESCO-listed Bhaktapur Durbar Square area.',
 'Free WiFi,Heritage Tours,Restaurant,Garden,Library,Rooftop Terrace',
 'heritage_courtyard.jpg'),

('Mountain Breeze Lodge', 'Nagarkot, Nepal', 2500.00, 4.5,
 'Perched at 2175m above sea level, this lodge offers breathtaking Himalayan sunrise views and a cozy mountain atmosphere.',
 'Free WiFi,Himalayan Views,Trekking Guide,Restaurant,Bonfire,Parking',
 'mountain_breeze.jpg'),

('Urban Nest Hotel', 'Thamel, Kathmandu', 1200.00, 4.2,
 'A modern budget-friendly hotel in the vibrant Thamel district, perfect for backpackers and adventure seekers.',
 'Free WiFi,24hr Reception,Rooftop,Restaurant,Tour Desk,Locker',
 'urban_nest.jpg'),

('Tranquil Valley Retreat', 'Chitwan, Nepal', 2800.00, 4.7,
 'An eco-friendly jungle retreat bordering Chitwan National Park, offering wildlife safari packages and nature experiences.',
 'Free WiFi,Safari Tours,Swimming Pool,Restaurant,Nature Walks,Elephant Bathing',
 'tranquil_valley.jpg');
 
 (' Kathmandu Marriott Hotel', 'Manakamana Marg, Naxal', 2500.00, 4.7,
 'Kathmandu Marriott Hotel is a premium 5-star hotel in the heart of Kathmandu, offering luxury comfort, elegant rooms, fine dining, and world-class hospitality for both business and leisure travelers.',
 'Pool – outdoor (kids),Airport shuttle ,Non-smoking rooms ,Spa, Free Wifi ,Room service ,Family rooms ,Free parking ,Bar, Exceptional Breakfast',
 'Kathmandu-Marriott-Hotel.jpg');

 ('Aarya Hotel and Spa - Eternal Heritage',)

