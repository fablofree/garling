-- Sample expenses only (admin user created via migrate.php or manually)
INSERT INTO users ( username, password, full_name, email, role, is_active ) VALUES
( 'admin', '$2y$10$sCs9dqEYmw2KDmqYJmXLEOm74ZOMa6t1zt7EQ8G/XoKcG6w3nbVwu', 'Administrator', 'admin@example.com', 'admin', 1 );

INSERT IGNORE INTO expenses (expense_date, category, description, amount) VALUES
('2025-01-15', 'Utilities',  'Electricity bill - January', 450.00),
('2025-01-20', 'Supplies',   'Workshop consumables', 220.50),
('2025-02-05', 'Rent',       'Workshop rent - February', 1200.00);
