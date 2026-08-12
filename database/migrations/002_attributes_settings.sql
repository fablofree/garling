-- Garage A. Lingiah — Attributes & Settings (MySQL)
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS app_settings (
    `key`       VARCHAR(100) PRIMARY KEY,
    `value`     TEXT,
    updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO app_settings (`key`, `value`) VALUES
('app_name',        'Garage A. Lingiah'),
('app_logo',        ''),
('app_brn',         ''),
('app_vat_reg',     ''),
('app_address',     ''),
('app_tel',         ''),
('app_email',       ''),
('currency_symbol', 'Rs'),
('vat_default',     '0')
ON DUPLICATE KEY UPDATE `key`=`key`;

CREATE TABLE IF NOT EXISTS vehicle_makes (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS vehicle_models (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    make_id    INT,
    name       VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_model_make (make_id, name),
    FOREIGN KEY (make_id) REFERENCES vehicle_makes(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS vehicle_types (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS vehicle_colours (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS part_categories (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS catalog_items (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT          NOT NULL,
    name        VARCHAR(200) NOT NULL,
    description TEXT,
    unit_price  DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES part_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO vehicle_makes (name) VALUES
('Toyota'),('Honda'),('Nissan'),('Ford'),('Hyundai'),('Kia'),('BMW'),
('Mercedes-Benz'),('Volkswagen'),('Suzuki'),('Peugeot'),('Renault'),('Mitsubishi');

INSERT IGNORE INTO vehicle_types (name) VALUES
('Car'),('SUV'),('Truck'),('Van'),('Motorcycle'),('Bus'),('Pick-up');

INSERT IGNORE INTO vehicle_colours (name) VALUES
('White'),('Black'),('Silver'),('Grey'),('Red'),('Blue'),('Green'),
('Yellow'),('Orange'),('Brown'),('Beige');

INSERT IGNORE INTO part_categories (name) VALUES
('Batteries'),('Tyres'),('Engine Oil'),('Filters'),('Brakes'),('Spark Plugs'),('Other');

SET FOREIGN_KEY_CHECKS = 1;
