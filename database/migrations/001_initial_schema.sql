-- Garage A. Lingiah - Initial Schema
-- PostgreSQL

CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(200),
    email VARCHAR(200),
    role VARCHAR(50) NOT NULL DEFAULT 'staff',
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Customers table
CREATE TABLE IF NOT EXISTS customers (
    id SERIAL PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    address TEXT,
    tel_home VARCHAR(50),
    tel_office VARCHAR(50),
    tel_mobile VARCHAR(50),
    fax VARCHAR(50),
    email VARCHAR(200),
    notes TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Vehicles table
CREATE TABLE IF NOT EXISTS vehicles (
    id SERIAL PRIMARY KEY,
    customer_id INTEGER NOT NULL REFERENCES customers(id) ON DELETE CASCADE,
    registration_no VARCHAR(50) NOT NULL,
    chassis_no VARCHAR(100),
    make VARCHAR(100),
    model VARCHAR(100),
    vehicle_type VARCHAR(100),
    colour VARCHAR(100),
    year INTEGER,
    distance_unit VARCHAR(10) NOT NULL DEFAULT 'km',
    servicing_frequency INTEGER DEFAULT 5000,
    notes TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Service entries table
CREATE TABLE IF NOT EXISTS service_entries (
    id SERIAL PRIMARY KEY,
    entry_date DATE NOT NULL DEFAULT CURRENT_DATE,
    vehicle_id INTEGER NOT NULL REFERENCES vehicles(id) ON DELETE RESTRICT,
    customer_id INTEGER NOT NULL REFERENCES customers(id) ON DELETE RESTRICT,
    odometer INTEGER,
    next_servicing INTEGER,
    remarks TEXT,
    entry_type VARCHAR(20) NOT NULL DEFAULT 'INVOICE',
    is_quotation BOOLEAN NOT NULL DEFAULT FALSE,
    is_completed BOOLEAN NOT NULL DEFAULT FALSE,
    delivery_date DATE,
    vat_percent NUMERIC(5,2) NOT NULL DEFAULT 0,
    discount_amount NUMERIC(10,2) NOT NULL DEFAULT 0,
    total_parts NUMERIC(10,2) NOT NULL DEFAULT 0,
    total_labour NUMERIC(10,2) NOT NULL DEFAULT 0,
    subtotal NUMERIC(10,2) NOT NULL DEFAULT 0,
    vat_amount NUMERIC(10,2) NOT NULL DEFAULT 0,
    total_cost NUMERIC(10,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Spare parts table (line items for service entries)
CREATE TABLE IF NOT EXISTS spare_parts (
    id SERIAL PRIMARY KEY,
    service_entry_id INTEGER NOT NULL REFERENCES service_entries(id) ON DELETE CASCADE,
    description VARCHAR(500) NOT NULL,
    quantity NUMERIC(10,2) NOT NULL DEFAULT 1,
    unit_price NUMERIC(10,2) NOT NULL DEFAULT 0,
    total_price NUMERIC(10,2) NOT NULL DEFAULT 0,
    sort_order INTEGER NOT NULL DEFAULT 0
);

-- Repairs / Labour table (line items for service entries)
CREATE TABLE IF NOT EXISTS repairs (
    id SERIAL PRIMARY KEY,
    service_entry_id INTEGER NOT NULL REFERENCES service_entries(id) ON DELETE CASCADE,
    description VARCHAR(500) NOT NULL,
    quantity NUMERIC(10,2) NOT NULL DEFAULT 1,
    unit_price NUMERIC(10,2) NOT NULL DEFAULT 0,
    total_price NUMERIC(10,2) NOT NULL DEFAULT 0,
    sort_order INTEGER NOT NULL DEFAULT 0
);

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
    id SERIAL PRIMARY KEY,
    service_entry_id INTEGER NOT NULL REFERENCES service_entries(id) ON DELETE CASCADE,
    payment_date DATE NOT NULL DEFAULT CURRENT_DATE,
    amount NUMERIC(10,2) NOT NULL,
    payment_method VARCHAR(20) NOT NULL DEFAULT 'CASH',
    cheque_number VARCHAR(100),
    reference VARCHAR(200),
    notes TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Expenses table
CREATE TABLE IF NOT EXISTS expenses (
    id SERIAL PRIMARY KEY,
    expense_date DATE NOT NULL DEFAULT CURRENT_DATE,
    category VARCHAR(100) NOT NULL,
    description VARCHAR(500) NOT NULL,
    amount NUMERIC(10,2) NOT NULL,
    reference VARCHAR(200),
    notes TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Indexes
CREATE INDEX IF NOT EXISTS idx_vehicles_customer_id ON vehicles(customer_id);
CREATE INDEX IF NOT EXISTS idx_vehicles_registration ON vehicles(registration_no);
CREATE INDEX IF NOT EXISTS idx_service_entries_vehicle_id ON service_entries(vehicle_id);
CREATE INDEX IF NOT EXISTS idx_service_entries_customer_id ON service_entries(customer_id);
CREATE INDEX IF NOT EXISTS idx_service_entries_entry_date ON service_entries(entry_date);
CREATE INDEX IF NOT EXISTS idx_spare_parts_service_entry_id ON spare_parts(service_entry_id);
CREATE INDEX IF NOT EXISTS idx_repairs_service_entry_id ON repairs(service_entry_id);
CREATE INDEX IF NOT EXISTS idx_payments_service_entry_id ON payments(service_entry_id);
CREATE INDEX IF NOT EXISTS idx_payments_payment_date ON payments(payment_date);
CREATE INDEX IF NOT EXISTS idx_expenses_expense_date ON expenses(expense_date);
