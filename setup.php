<?php

/**
 * Garage A. Lingiah — Database Setup Script
 *
 * Run this once to create tables and seed data:
 *   php setup.php
 *
 * Or visit /setup in your browser (then delete this file after use).
 */

define('ROOT_PATH', __DIR__);

$config = require ROOT_PATH . '/config/database.php';

$dsn = sprintf(
    '%s:host=%s;port=%s;dbname=%s',
    $config['driver'],
    $config['host'],
    $config['port'],
    $config['database']
);

try {
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "Connected to database successfully.\n\n";
} catch (PDOException $e) {
    echo "ERROR: Cannot connect to database.\n";
    echo "DSN: $dsn\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "\nPlease check your config/database.php settings.\n";
    exit(1);
}

// Run migration
$migration = ROOT_PATH . '/database/migrations/001_initial_schema.sql';
if (file_exists($migration)) {
    $sql = file_get_contents($migration);
    try {
        $pdo->exec($sql);
        echo "Migration applied: 001_initial_schema.sql\n";
    } catch (PDOException $e) {
        echo "Migration warning: " . $e->getMessage() . "\n";
    }
}

// Create default admin user with a proper password hash
$defaultPassword = 'admin123';
$hashedPassword  = password_hash($defaultPassword, PASSWORD_BCRYPT, ['cost' => 12]);
try {
    $stmt = $pdo->prepare(
        "INSERT INTO users (username, password, full_name, email, role)
         VALUES (:username, :password, :full_name, :email, :role)
         ON CONFLICT (username) DO NOTHING"
    );
    $stmt->execute([
        ':username'  => 'admin',
        ':password'  => $hashedPassword,
        ':full_name' => 'Administrator',
        ':email'     => 'admin@garagelingiah.com',
        ':role'      => 'admin',
    ]);
    echo "Admin user created (username: admin, password: $defaultPassword)\n";
} catch (PDOException $e) {
    echo "Admin user warning: " . $e->getMessage() . "\n";
}

// Run seed (sample expenses / reference data)
$seed = ROOT_PATH . '/database/seeds/001_seed_data.sql';
if (file_exists($seed)) {
    $sql = file_get_contents($seed);
    if (trim($sql) !== '') {
        try {
            $pdo->exec($sql);
            echo "Seed applied: 001_seed_data.sql\n";
        } catch (PDOException $e) {
            echo "Seed warning: " . $e->getMessage() . "\n";
        }
    }
}

echo "\nSetup complete!\n";
echo "Default login: admin / admin123\n";
echo "\nIMPORTANT: Delete this file after setup for security.\n";
