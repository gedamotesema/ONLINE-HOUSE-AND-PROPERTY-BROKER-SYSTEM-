-- seed.sql

USE rental_broker;

-- Example Users: bcrypt hashes generated for sample passwords (php: password_hash)
INSERT INTO users (username, email, password_hash, role, created_at, status) VALUES
('admin', 'admin@local.com', '$2y$10$3AFfjQvM8vRALRbYWBJauejRkAKBAfOLmwO0U3jWUMylpn32bO0da', 'admin', NOW(), 'active'),        -- password: adminpass
('alice', 'alice@local.com', '$2y$10$GQY1IuYkI1hI1/IY2V0V5uCtX4/0f15V8GP2A0EgWyvI5ytNYFR98C', 'owner', NOW(), 'active'),       -- password: alicepass
('bob', 'bob@local.com', '$2y$10$nSU6hTyaP2elUZhw8U3SlOjunMUk3vJNYBzUOr4UV/Usuv9g0VKGu', 'renter', NOW(), 'active'),           -- password: bobpass
('eve', 'eve@local.com', '$2y$10$KI5oJH4gYvurJPo.m5Jq6uGzYgPAh3KkbuFRBzhxPmoWiBhzHq4K.', 'owner', NOW(), 'active');            -- password: evepass

-- Example Properties (images are filenames you manually place in /uploads/properties/)
INSERT INTO properties (owner_id, title, location, price, type, description, images, availability, created_at) VALUES
(2, 'Modern Condo by City Center', 'Addis Ababa', 650.00, 'condo', 'Spacious, modern condo unit with balcony and good view.', 'condo1.jpg,condo2.jpg', 'available', NOW()),
(2, 'Cute Family Apartment', 'Adama', 400.00, 'apartment', 'Family-friendly 2-bedroom apartment near schools.', 'apt1.jpg,apt2.jpg', 'available', NOW()),
(4, 'Luxury Villa Oasis', 'Bole', 2500.00, 'house', 'Private villa in Bole, full garden and garage, 5 bedrooms.', 'villa1.jpg,villa2.jpg', 'available', NOW()),
(4, 'Budget Studio', 'Mekelle', 180.00, 'apartment', 'Affordable studio for students, security and WiFi included.', 'studio1.jpg', 'not available', NOW());

-- Example Favorites (bob favorites Alice's and Eve's properties)
INSERT INTO favorites (user_id, property_id) VALUES
(3, 1), (3, 3);

-- Example Inquiries (Bob inquires about the condo from Alice)
INSERT INTO inquiries (property_id, sender_id, owner_id, message, status, created_at) VALUES
(1, 3, 2, 'Hi, is the modern condo available for December 15th move-in?', 'pending', NOW());

-- Logs example (optional)
INSERT INTO logs (event, user_id, details, created_at) VALUES
('User registration', 3, 'bob@local.com signed up.', NOW()),
('Property added', 2, 'Alice added Modern Condo.', NOW());
