-- Garage A. Lingiah — Initial Schema (MySQL)
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(100) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    full_name  VARCHAR(200),
    email      VARCHAR(200),
    role       VARCHAR(50)  NOT NULL DEFAULT 'staff',
    is_active  TINYINT(1)   NOT NULL DEFAULT 1,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS customers (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(200) NOT NULL,
    address    TEXT,
    tel_home   VARCHAR(50),
    tel_office VARCHAR(50),
    tel_mobile VARCHAR(50),
    fax        VARCHAR(50),
    email      VARCHAR(200),
    brn        VARCHAR(100),
    vat_number VARCHAR(100),
    notes      TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS vehicles (
    id                   INT AUTO_INCREMENT PRIMARY KEY,
    customer_id          INT          NOT NULL,
    registration_no      VARCHAR(50)  NOT NULL,
    chassis_no           VARCHAR(100),
    make                 VARCHAR(100),
    model                VARCHAR(100),
    vehicle_type         VARCHAR(100),
    colour               VARCHAR(100),
    year                 INT,
    distance_unit        VARCHAR(10)  NOT NULL DEFAULT 'km',
    servicing_frequency  INT          DEFAULT 5000,
    notes                TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS service_entries (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    entry_date      DATE         NOT NULL,
    vehicle_id      INT          NOT NULL,
    customer_id     INT          NOT NULL,
    odometer        INT,
    next_servicing  INT,
    remarks         TEXT,
    entry_type      VARCHAR(20)  NOT NULL DEFAULT 'INVOICE',
    is_quotation    TINYINT(1)   NOT NULL DEFAULT 0,
    is_completed    TINYINT(1)   NOT NULL DEFAULT 0,
    delivery_date   DATE,
    vat_percent     DECIMAL(5,2) NOT NULL DEFAULT 0,
    discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_parts     DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_labour    DECIMAL(10,2) NOT NULL DEFAULT 0,
    subtotal        DECIMAL(10,2) NOT NULL DEFAULT 0,
    vat_amount      DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_cost      DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id)  REFERENCES vehicles(id)  ON DELETE RESTRICT,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS spare_parts (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    service_entry_id INT          NOT NULL,
    description      VARCHAR(500) NOT NULL,
    amount           DECIMAL(10,2) NOT NULL DEFAULT 0,
    sort_order       INT          NOT NULL DEFAULT 0,
    FOREIGN KEY (service_entry_id) REFERENCES service_entries(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS repairs (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    service_entry_id INT          NOT NULL,
    description      VARCHAR(500) NOT NULL,
    amount           DECIMAL(10,2) NOT NULL DEFAULT 0,
    sort_order       INT          NOT NULL DEFAULT 0,
    FOREIGN KEY (service_entry_id) REFERENCES service_entries(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS invoices (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    invoice_number   VARCHAR(20)  NOT NULL UNIQUE,
    service_entry_id INT          NOT NULL UNIQUE,
    generated_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_entry_id) REFERENCES service_entries(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS quotations (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    quotation_number    VARCHAR(20)  NOT NULL UNIQUE,
    service_entry_id    INT          NOT NULL UNIQUE,
    generated_at        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_entry_id) REFERENCES service_entries(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS payments (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    service_entry_id INT          NOT NULL,
    payment_date     DATE         NOT NULL,
    amount           DECIMAL(10,2) NOT NULL,
    payment_method   VARCHAR(20)  NOT NULL DEFAULT 'CASH',
    cheque_number    VARCHAR(100),
    reference        VARCHAR(200),
    notes            TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_entry_id) REFERENCES service_entries(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS expenses (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    expense_date DATE         NOT NULL,
    category     VARCHAR(100) NOT NULL,
    description  VARCHAR(500) NOT NULL,
    amount       DECIMAL(10,2) NOT NULL,
    reference    VARCHAR(200),
    notes        TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Indexes
CREATE INDEX idx_vehicles_customer   ON vehicles(customer_id);
CREATE INDEX idx_vehicles_reg        ON vehicles(registration_no);
CREATE INDEX idx_se_vehicle          ON service_entries(vehicle_id);
CREATE INDEX idx_se_customer         ON service_entries(customer_id);
CREATE INDEX idx_se_date             ON service_entries(entry_date);
CREATE INDEX idx_payments_se         ON payments(service_entry_id);
CREATE INDEX idx_payments_date       ON payments(payment_date);
CREATE INDEX idx_expenses_date       ON expenses(expense_date);

SET FOREIGN_KEY_CHECKS = 1;
