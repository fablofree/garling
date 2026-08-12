-- Sample expenses only (admin user created via migrate.php or manually)
INSERT IGNORE INTO expenses (expense_date, category, description, amount) VALUES
('2025-01-15', 'Utilities',  'Electricity bill - January', 450.00),
('2025-01-20', 'Supplies',   'Workshop consumables', 220.50),
('2025-02-05', 'Rent',       'Workshop rent - February', 1200.00);
