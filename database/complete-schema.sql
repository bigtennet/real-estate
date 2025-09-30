-- Complete Database Schema for Premium Real Estate Website
-- This file contains all tables needed for deployment

-- Properties table (admin is the only owner)
CREATE TABLE IF NOT EXISTS properties (
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
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL,
    setting_type ENUM('text', 'number', 'boolean', 'json') DEFAULT 'text',
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- File uploads table
CREATE TABLE IF NOT EXISTS file_uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type ENUM('image', 'video', 'document') NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
);

-- About content table
CREATE TABLE IF NOT EXISTS about_content (
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
CREATE TABLE IF NOT EXISTS realtor_profile (
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

-- Subscribers table for mailing list
CREATE TABLE IF NOT EXISTS subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(255) NULL,
    status ENUM('active', 'unsubscribed', 'bounced') DEFAULT 'active',
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Email campaigns table
CREATE TABLE IF NOT EXISTS email_campaigns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    property_ids JSON NULL,
    status ENUM('draft', 'sending', 'sent', 'failed') DEFAULT 'draft',
    sent_count INT DEFAULT 0,
    total_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sent_at TIMESTAMP NULL
);

-- SMTP settings table
CREATE TABLE IF NOT EXISTS smtp_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default site settings
INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES
('site_name', 'Premium Real Estate', 'text', 'Website name'),
('site_description', 'Your trusted partner in finding the perfect property. We specialize in luxury real estate and exceptional service.', 'text', 'Website description'),
('contact_phone', '+234 (0) 123-456-7890', 'text', 'Contact phone number'),
('contact_email', 'info@premiumrealestate.ng', 'text', 'Contact email address'),
('contact_address', '123 Victoria Island, Lagos, Nigeria', 'text', 'Office address'),
('whatsapp_number', '+2341234567890', 'text', 'WhatsApp contact number'),
('currency', 'NGN', 'text', 'Currency symbol'),
('currency_name', 'Naira', 'text', 'Currency name'),
('site_logo', '', 'text', 'Site logo URL'),
('facebook_url', '', 'text', 'Facebook page URL'),
('twitter_url', '', 'text', 'Twitter profile URL'),
('instagram_url', '', 'text', 'Instagram profile URL'),
('linkedin_url', '', 'text', 'LinkedIn profile URL'),
('youtube_url', '', 'text', 'YouTube channel URL'),
('whatsapp_business', '', 'text', 'WhatsApp Business number');

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

-- Insert sample properties
INSERT INTO properties (title, description, price, location, bedrooms, bathrooms, area, type, images, features) VALUES
('Luxury Modern Villa', 'Stunning modern villa with panoramic views, premium finishes, and smart home technology.', 250000000.00, 'Lagos, Nigeria', 5, 4, 3500.00, 'VILLA', 
'["https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&h=600&fit=crop"]', 
'["Pool", "Garden", "Garage", "Security", "Smart Home"]'),

('Executive Apartment', 'Contemporary apartment in the heart of Victoria Island with stunning city views.', 85000000.00, 'Victoria Island, Lagos', 3, 2, 1200.00, 'APARTMENT', 
'["https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800&h=600&fit=crop"]', 
'["City View", "Balcony", "Modern Kitchen", "Parking", "Security"]'),

('Luxury Townhouse', 'Spacious townhouse with private garden and premium amenities in a gated community.', 180000000.00, 'Ikoyi, Lagos', 4, 3, 2800.00, 'TOWNHOUSE', 
'["https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800&h=600&fit=crop"]', 
'["Private Garden", "Gated Community", "Modern Design", "Parking", "Security"]');

-- Insert default SMTP settings
INSERT INTO smtp_settings (setting_key, setting_value, description) VALUES
('smtp_host', '', 'SMTP server hostname'),
('smtp_port', '587', 'SMTP server port'),
('smtp_username', '', 'SMTP username'),
('smtp_password', '', 'SMTP password'),
('smtp_encryption', 'tls', 'SMTP encryption (tls, ssl, or none)'),
('smtp_from_email', '', 'From email address'),
('smtp_from_name', '', 'From name'),
('smtp_enabled', '0', 'Enable SMTP (1 for enabled, 0 for disabled)');

-- Insert sample subscribers
INSERT INTO subscribers (email, name) VALUES
('john.doe@example.com', 'John Doe'),
('jane.smith@example.com', 'Jane Smith'),
('mike.wilson@example.com', 'Mike Wilson');
