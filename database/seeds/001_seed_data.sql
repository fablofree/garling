-- Seed data for Garage A. Lingiah
-- Admin user is inserted by setup.php with a proper PHP-generated password hash.

-- Sample expense categories
INSERT INTO expenses (expense_date, category, description, amount) VALUES
('2025-01-15', 'Utilities', 'Electricity bill - January', 450.00),
('2025-01-20', 'Supplies', 'Workshop consumables', 220.50),
('2025-02-05', 'Rent', 'Workshop rent - February', 1200.00)
ON CONFLICT DO NOTHING;
