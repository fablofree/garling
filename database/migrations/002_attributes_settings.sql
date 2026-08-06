-- ============================================================
-- Migration 002: App Settings + Vehicle Attributes + Catalog
-- ============================================================

-- App settings
CREATE TABLE IF NOT EXISTS app_settings (
    key        VARCHAR(100) PRIMARY KEY,
    value      TEXT,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO app_settings(key, value)
VALUES
    ('app_name',         'Garage A. Lingiah'),
    ('app_logo',         ''),
    ('currency_symbol',  'Rs'),
    ('vat_default',      '0')
ON CONFLICT(key) DO NOTHING;

-- Vehicle attribute lookup tables
CREATE TABLE IF NOT EXISTS vehicle_makes (
    id         SERIAL PRIMARY KEY,
    name       VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS vehicle_models (
    id         SERIAL PRIMARY KEY,
    make_id    INT REFERENCES vehicle_makes(id) ON DELETE SET NULL,
    name       VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(make_id, name)
);

CREATE TABLE IF NOT EXISTS vehicle_types (
    id         SERIAL PRIMARY KEY,
    name       VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS vehicle_colours (
    id         SERIAL PRIMARY KEY,
    name       VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Seed makes
INSERT INTO vehicle_makes(name)
VALUES
    ('Toyota'), ('Honda'), ('Nissan'), ('Ford'), ('Hyundai'),
    ('Kia'), ('BMW'), ('Mercedes-Benz'), ('Volkswagen'), ('Suzuki'),
    ('Peugeot'), ('Renault'), ('Mitsubishi'), ('Mazda'), ('Subaru')
ON CONFLICT(name) DO NOTHING;

-- Seed types
INSERT INTO vehicle_types(name)
VALUES
    ('Car'), ('SUV'), ('Truck'), ('Van'), ('Motorcycle'),
    ('Bus'), ('Pick-up'), ('Minivan'), ('Hatchback'), ('Sedan')
ON CONFLICT(name) DO NOTHING;

-- Seed colours
INSERT INTO vehicle_colours(name)
VALUES
    ('White'), ('Black'), ('Silver'), ('Grey'), ('Red'),
    ('Blue'), ('Green'), ('Yellow'), ('Orange'), ('Brown'), ('Beige')
ON CONFLICT(name) DO NOTHING;

-- Part catalog
CREATE TABLE IF NOT EXISTS part_categories (
    id         SERIAL PRIMARY KEY,
    name       VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS catalog_items (
    id          SERIAL PRIMARY KEY,
    category_id INT NOT NULL REFERENCES part_categories(id) ON DELETE CASCADE,
    name        VARCHAR(200) NOT NULL,
    description TEXT,
    unit_price  NUMERIC(10,2) NOT NULL DEFAULT 0,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO part_categories(name)
VALUES
    ('Batteries'), ('Tyres'), ('Engine Oil'), ('Filters'),
    ('Brakes'), ('Spark Plugs'), ('Belts & Hoses'), ('Bulbs'), ('Other')
ON CONFLICT(name) DO NOTHING;
