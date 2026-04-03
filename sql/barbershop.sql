-- ============================================================
-- Barbershop Booking Platform — Database Schema
-- Team 5 | Lab Group Activity
-- ============================================================

CREATE DATABASE IF NOT EXISTS barbershop_db;
USE barbershop_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Staff table (optional: staff member selection)
CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(100) NOT NULL,
    bio TEXT,
    photo VARCHAR(255),
    available TINYINT(1) DEFAULT 1
);

-- Service categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50)
);

-- Services table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(6,2) NOT NULL,
    duration_minutes INT NOT NULL DEFAULT 30,
    image VARCHAR(255),
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Appointments table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    staff_id INT,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    notes TEXT,
    status ENUM('pending','confirmed','cancelled','completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE SET NULL
);

-- ============================================================
-- Seed Data
-- ============================================================

INSERT INTO categories (name, description, icon) VALUES
('Haircut', 'Classic and modern cuts for every style', '✂️'),
('Beard & Shave', 'Beard trims, shaping and hot-towel shaves', '🪒'),
('Styling', 'Blow-dry, texturing and finishing looks', '💨'),
('Coloring', 'Full color, highlights and toning treatments', '🎨'),
('Treatments', 'Deep conditioning and scalp care', '🌿');

INSERT INTO staff (name, role, bio) VALUES
('Marco Rivera', 'Master Barber', '12 years of experience in classic and modern cuts.'),
('Sofia Lenz', 'Color Specialist', 'Expert in balayage, highlights and creative color.'),
('James Cole', 'Stylist', 'Known for precision fades and beard sculpting.');

INSERT INTO services (category_id, name, description, price, duration_minutes) VALUES
(1, 'Classic Cut', 'Traditional scissor cut with a clean finish.', 25.00, 30),
(1, 'Skin Fade', 'Precision fade from skin to desired length.', 30.00, 45),
(1, 'Kids Cut', 'Gentle cut for children up to 12 years.', 18.00, 20),
(2, 'Beard Trim', 'Shape and define your beard line.', 15.00, 20),
(2, 'Full Shave', 'Hot-towel straight-razor shave experience.', 28.00, 40),
(2, 'Beard Sculpt', 'Full beard shaping and styling with oil.', 22.00, 30),
(3, 'Blow-Dry Style', 'Professional blow-dry and finish.', 20.00, 25),
(3, 'Textured Look', 'Matte or glossy textured styling.', 18.00, 20),
(4, 'Full Color', 'All-over single color application.', 55.00, 90),
(4, 'Highlights', 'Partial or full foil highlights.', 65.00, 120),
(4, 'Toner', 'Gloss or toning treatment for vibrancy.', 30.00, 45),
(5, 'Deep Condition', 'Intensive moisture mask treatment.', 25.00, 30),
(5, 'Scalp Treatment', 'Exfoliating scalp scrub and massage.', 30.00, 35);
