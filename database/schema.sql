-- Real Estate Database Schema
CREATE DATABASE IF NOT EXISTS real_estate_db;
USE real_estate_db;

-- Properties table (admin is the only owner)
CREATE TABLE properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(15,2) NOT NULL,
    location VARCHAR(255) NOT NULL,
    bedrooms INT NOT NULL,
    bathrooms INT NOT NULL,
    area DECIMAL(10,2) NOT NULL,
    type ENUM('HOUSE', 'APARTMENT', 'VILLA', 'CONDO', 'TOWNHOUSE', 'LAND', 'COMMERCIAL') NOT NULL,
    status ENUM('AVAILABLE', 'SOLD', 'RENTED', 'PENDING') DEFAULT 'AVAILABLE',
    images JSON,
    features JSON,
    video_url VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Site settings table
CREATE TABLE site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'number', 'boolean', 'json') DEFAULT 'text',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- File uploads table
CREATE TABLE file_uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type ENUM('image', 'video') NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
);

-- Insert sample data (admin is the only owner)
INSERT INTO properties (title, description, price, location, bedrooms, bathrooms, area, type, images, features) VALUES
('Luxury Modern Villa', 'Stunning modern villa with panoramic views, premium finishes, and smart home technology.', 250000000.00, 'Lagos, Nigeria', 5, 4, 3500.00, 'VILLA', 
'["https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&h=600&fit=crop"]', 
'["Pool", "Garden", "Garage", "Security", "Smart Home"]'),

('Downtown Penthouse', 'Exclusive penthouse with city views, high-end appliances, and concierge service.', 180000000.00, 'Abuja, Nigeria', 3, 2, 2200.00, 'APARTMENT',
'["https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800&h=600&fit=crop"]',
'["City View", "Balcony", "Gym", "Concierge", "Parking"]'),

('Beachfront Condo', 'Beautiful beachfront condo with ocean views and direct beach access.', 120000000.00, 'Port Harcourt, Nigeria', 2, 2, 1500.00, 'CONDO',
'["https://images.unsplash.com/photo-1600607687644-c7171b42498b?w=800&h=600&fit=crop"]',
'["Ocean View", "Beach Access", "Pool", "Parking", "Fitness Center"]');

-- About page content table
CREATE TABLE about_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(100) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image_url VARCHAR(500) NULL,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Realtor profile table
CREATE TABLE realtor_profile (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    bio TEXT NOT NULL,
    profile_image VARCHAR(500) NULL,
    phone VARCHAR(50) NULL,
    email VARCHAR(255) NULL,
    whatsapp VARCHAR(50) NULL,
    license_number VARCHAR(100) NULL,
    experience_years INT DEFAULT 0,
    specialties JSON NULL,
    achievements JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default about content
INSERT INTO about_content (section_key, title, content, display_order) VALUES
('hero', 'About Premium Real Estate', 'We are passionate about helping you find the perfect property and making your real estate dreams come true.', 1),
('story', 'Our Story', 'Founded with a vision to revolutionize real estate in Nigeria, Premium Real Estate has been connecting families with their dream homes for over a decade. Our commitment to excellence and personalized service sets us apart in the industry.', 2),
('mission', 'Our Mission', 'To provide exceptional real estate services that exceed expectations, helping our clients make informed decisions about their most important investments.', 3),
('values', 'Our Values', 'Integrity, transparency, and client satisfaction are at the core of everything we do. We believe in building lasting relationships based on trust and mutual respect.', 4);

-- Insert default realtor profile
INSERT INTO realtor_profile (full_name, title, bio, phone, email, whatsapp, license_number, experience_years, specialties, achievements) VALUES
('Prince Ademuyiwa Edward Ojo', 'Senior Real Estate Consultant', 'With over 10 years of experience in the Nigerian real estate market, Prince Ademuyiwa Edward Ojo has helped hundreds of families find their perfect homes. His expertise spans luxury residential properties, commercial real estate, and investment opportunities across Lagos, Abuja, and Port Harcourt.', '+234 (0) 123-456-7890', 'prince@premiumrealestate.ng', '+2341234567890', 'REALTOR-2024-001', 10, 
'["Luxury Residential", "Commercial Real Estate", "Investment Properties", "Property Management"]', 
'["Top Performer 2023", "Client Satisfaction Award", "Luxury Property Specialist"]');

-- Insert default site settings
INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES
('site_name', 'Premium Real Estate', 'text', 'Website name'),
('site_description', 'Find your dream home with our premium real estate listings in Nigeria', 'text', 'Website description'),
('site_logo', '', 'text', 'Site logo URL'),
('contact_phone', '+234 (0) 123-456-7890', 'text', 'Contact phone number'),
('contact_email', 'info@premiumrealestate.ng', 'text', 'Contact email address'),
('contact_address', '123 Victoria Island, Lagos, Nigeria', 'text', 'Office address'),
('whatsapp_number', '+2341234567890', 'text', 'WhatsApp contact number'),
('currency', 'NGN', 'text', 'Currency symbol'),
('currency_name', 'Naira', 'text', 'Currency name'),
('properties_per_page', '12', 'number', 'Number of properties per page'),
('enable_video_uploads', 'true', 'boolean', 'Enable video uploads for properties'),
('max_images_per_property', '8', 'number', 'Maximum images per property'),
('max_video_size_mb', '100', 'number', 'Maximum video file size in MB'),
('facebook_url', '', 'text', 'Facebook page URL'),
('twitter_url', '', 'text', 'Twitter profile URL'),
('instagram_url', '', 'text', 'Instagram profile URL'),
('linkedin_url', '', 'text', 'LinkedIn profile URL'),
('youtube_url', '', 'text', 'YouTube channel URL'),
('whatsapp_business', '', 'text', 'WhatsApp Business number');
