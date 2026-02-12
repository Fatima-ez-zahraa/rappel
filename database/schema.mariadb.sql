-- Database Schema for Rappelez-moi (MariaDB Version)
CREATE DATABASE IF NOT EXISTS rappel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rappel;

-- 1. Table: user_profiles
CREATE TABLE IF NOT EXISTS user_profiles (
    id CHAR(36) PRIMARY KEY, -- UUID (généré par PHP ou UUID() de MariaDB >= 10.7)
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    job_title VARCHAR(100),
    company_name VARCHAR(255),
    siret VARCHAR(20) UNIQUE,
    legal_form VARCHAR(50),
    creation_year INT,
    address TEXT,
    zip VARCHAR(20),
    city VARCHAR(100),
    phone VARCHAR(20),
    role VARCHAR(20) DEFAULT 'provider', -- 'admin' | 'provider'
    subscription_status VARCHAR(20) DEFAULT 'inactive', -- 'active' | 'inactive'
    stripe_customer_id VARCHAR(255) UNIQUE,
    verification_code VARCHAR(100),
    is_verified BOOLEAN DEFAULT FALSE,
    sectors JSON, -- Stocké comme JSON ["Secteur 1", "Secteur 2"]
    description TEXT,
    zone VARCHAR(100),
    certifications JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Index pour les recherches fréquentes
CREATE INDEX idx_user_email ON user_profiles(email);
CREATE INDEX idx_user_siret ON user_profiles(siret);

-- 2. Table: leads
CREATE TABLE IF NOT EXISTS leads (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    sector VARCHAR(100),
    need TEXT,
    budget DECIMAL(12, 2) DEFAULT 0,
    status VARCHAR(20) DEFAULT 'pending', -- 'pending' | 'assigned' | 'completed'
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Table: lead_assignments (Pivot table)
CREATE TABLE IF NOT EXISTS lead_assignments (
    id CHAR(36) PRIMARY KEY,
    lead_id CHAR(36) NOT NULL,
    provider_id CHAR(36) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES user_profiles(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Table: quotes
CREATE TABLE IF NOT EXISTS quotes (
    id CHAR(36) PRIMARY KEY,
    provider_id CHAR(36) NOT NULL,
    client_name VARCHAR(255),
    project_name VARCHAR(255),
    amount DECIMAL(12, 2),
    items_count INT DEFAULT 1,
    status VARCHAR(20) DEFAULT 'attente_client', -- 'attente_client' | 'signe' | 'refuse'
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (provider_id) REFERENCES user_profiles(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Table: subscription_plans
CREATE TABLE IF NOT EXISTS subscription_plans (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    stripe_price_id VARCHAR(255) UNIQUE NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'EUR',
    features JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Initial Data Seeding (Plans)
INSERT INTO subscription_plans (id, name, stripe_price_id, price, features)
VALUES 
(UUID(), 'Starter', 'price_starter_id', 29.99, '["10 leads / mois", "Support email"]'),
(UUID(), 'Business', 'price_business_id', 99.99, '["50 leads / mois", "Support prioritaire"]'),
(UUID(), 'Pack Pro', 'price_pack_pro_id', 49.99, '["25 leads / mois", "Statistiques avancées"]')
ON DUPLICATE KEY UPDATE price=VALUES(price);