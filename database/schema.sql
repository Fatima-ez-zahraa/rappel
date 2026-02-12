-- Database Schema for Rappelez-moi

-- 1. Table: user_profiles
CREATE TABLE IF NOT EXISTS user_profiles (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL, -- Hashed
    first_name TEXT,
    last_name TEXT,
    job_title TEXT,
    company_name TEXT,
    siret TEXT UNIQUE,
    legal_form TEXT,
    creation_year INTEGER,          
    address TEXT,
    zip TEXT,
    city TEXT,
    phone TEXT,
    role TEXT DEFAULT 'provider', -- 'admin' | 'provider'
    subscription_status TEXT DEFAULT 'inactive', -- 'active' | 'inactive'
    stripe_customer_id TEXT UNIQUE,
    verification_code TEXT,
    is_verified BOOLEAN DEFAULT FALSE,
    sectors TEXT[], -- Array of strings (PostgreSQL TEXT array)
    description TEXT,
    zone TEXT,
    certifications TEXT[], -- Array of strings
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- 2. Table: leads
CREATE TABLE IF NOT EXISTS leads (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name TEXT NOT NULL,
    email TEXT,
    phone TEXT,
    address TEXT,
    sector TEXT, -- 'Assurance', 'Rénovation', etc.
    need TEXT,
    budget DECIMAL(12, 2) DEFAULT 0,
    status TEXT DEFAULT 'pending', -- 'pending' | 'assigned' | 'completed'
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- 3. Table: lead_assignments (Pivot table)
CREATE TABLE IF NOT EXISTS lead_assignments (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    lead_id UUID REFERENCES leads(id) ON DELETE CASCADE,
    provider_id UUID REFERENCES user_profiles(id) ON DELETE CASCADE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- 4. Table: quotes
CREATE TABLE IF NOT EXISTS quotes (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    provider_id UUID REFERENCES user_profiles(id) ON DELETE CASCADE,
    client_name TEXT,
    project_name TEXT,
    amount DECIMAL(12, 2),
    items_count INTEGER DEFAULT 1,
    status TEXT DEFAULT 'attente_client', -- 'attente_client' | 'signe' | 'refuse'
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- 5. Table: subscription_plans
CREATE TABLE IF NOT EXISTS subscription_plans (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name TEXT UNIQUE NOT NULL,
    stripe_price_id TEXT UNIQUE NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    currency TEXT DEFAULT 'EUR',
    features TEXT[],
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Indices for performance
CREATE INDEX IF NOT EXISTS idx_user_email ON user_profiles(email);
CREATE INDEX IF NOT EXISTS idx_user_siret ON user_profiles(siret);
CREATE INDEX IF NOT EXISTS idx_lead_status ON leads(status);
CREATE INDEX IF NOT EXISTS idx_lead_sector ON leads(sector);
CREATE INDEX IF NOT EXISTS idx_assignment_provider ON lead_assignments(provider_id);
CREATE INDEX IF NOT EXISTS idx_quote_provider ON quotes(provider_id);

-- Initial Data Seeding
INSERT INTO subscription_plans (name, stripe_price_id, price, features)
VALUES 
('Starter', 'price_starter_id', 29.99, ARRAY['10 leads / mois', 'Support email']),
('Business', 'price_business_id', 99.99, ARRAY['50 leads / mois', 'Support prioritaire']),
('Pack Pro', 'price_pack_pro_id', 49.99, ARRAY['25 leads / mois', 'Statistiques avancées'])
ON CONFLICT (name) DO NOTHING;
