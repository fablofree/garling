<?php

declare(strict_types=1);

/**
 * Container-level migration & seed runner.
 *
 * - Tracks every applied migration in  schema_migrations (by filename).
 * - Tracks every applied seed       in  schema_seeds     (by filename).
 * - Ensures the default admin user exists after all migrations are done.
 * - Safe to run on every container start: already-applied files are skipped.
 * - Reads connection details from the same DB_* env vars used by the app.
 */

// ── Connection ────────────────────────────────────────────────────────────────
$host     = getenv('DB_HOST')     ?: 'db';
$port     = getenv('DB_PORT')     ?: '5432';
$database = getenv('DB_DATABASE') ?: 'garage_lingiah';
$username = getenv('DB_USERNAME') ?: 'postgres';
$password = getenv('DB_PASSWORD') ?: 'postgres';

$dsn = "pgsql:host={$host};port={$port};dbname={$database}";

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    fwrite(STDERR, "[migrate] FATAL: Cannot connect – " . $e->getMessage() . "\n");
    exit(1);
}

log_msg("Connected to {$database}@{$host}:{$port}");

// ── Tracking tables ───────────────────────────────────────────────────────────
$pdo->exec("
    CREATE TABLE IF NOT EXISTS schema_migrations (
        id         SERIAL      PRIMARY KEY,
        migration  VARCHAR(255) NOT NULL UNIQUE,
        applied_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP
    );
    CREATE TABLE IF NOT EXISTS schema_seeds (
        id         SERIAL      PRIMARY KEY,
        seed       VARCHAR(255) NOT NULL UNIQUE,
        applied_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP
    );
");

// ── Migrations ────────────────────────────────────────────────────────────────
$migrationsDir = __DIR__ . '/../database/migrations';
run_sql_files($pdo, $migrationsDir, 'schema_migrations', 'migration', 'migration');

// ── Seeds ─────────────────────────────────────────────────────────────────────
$seedsDir = __DIR__ . '/../database/seeds';
run_sql_files($pdo, $seedsDir, 'schema_seeds', 'seed', 'seed');

// ── Default admin user ────────────────────────────────────────────────────────
// Run after migrations so the `users` table is guaranteed to exist.
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    $stmt->execute();
    if ((int) $stmt->fetchColumn() === 0) {
        $hash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
        $pdo->prepare("
            INSERT INTO users (username, password, full_name, email, role)
            VALUES ('admin', :pw, 'Administrator', 'admin@garagelingiah.com', 'admin')
            ON CONFLICT (username) DO NOTHING
        ")->execute([':pw' => $hash]);
        log_msg("Admin user created  (username: admin / password: admin123)");
    } else {
        log_msg("Admin user already exists – skipping.");
    }
} catch (PDOException $e) {
    log_msg("WARNING: Could not create admin user – " . $e->getMessage());
}

log_msg("Migration runner finished.");

// ── Helpers ───────────────────────────────────────────────────────────────────

function run_sql_files(
    PDO    $pdo,
    string $dir,
    string $trackingTable,
    string $trackingColumn,
    string $label
): void {
    if (!is_dir($dir)) {
        log_msg("Directory not found, skipping: {$dir}");
        return;
    }

    $files = glob($dir . '/*.sql');
    if ($files === false || count($files) === 0) {
        log_msg("No {$label} files found in {$dir}");
        return;
    }

    sort($files); // apply alphabetically / numerically (001_, 002_, …)

    foreach ($files as $file) {
        $name = basename($file);
        $sql  = trim((string) file_get_contents($file));

        if ($sql === '') {
            log_msg("[{$label}] {$name} — empty, skipping.");
            continue;
        }

        // Check tracking table
        $check = $pdo->prepare(
            "SELECT COUNT(*) FROM {$trackingTable} WHERE {$trackingColumn} = ?"
        );
        $check->execute([$name]);

        if ((int) $check->fetchColumn() > 0) {
            log_msg("[{$label}] {$name} — already applied, skipping.");
            continue;
        }

        log_msg("[{$label}] {$name} — applying …");
        try {
            $pdo->exec($sql);
            $pdo->prepare(
                "INSERT INTO {$trackingTable} ({$trackingColumn}) VALUES (?)
                 ON CONFLICT DO NOTHING"
            )->execute([$name]);
            log_msg("[{$label}] {$name} — done.");
        } catch (PDOException $e) {
            // Log the error but do NOT mark as applied so the next restart retries.
            // All DDL uses IF NOT EXISTS so retrying is safe.
            log_msg("[{$label}] {$name} — ERROR: " . $e->getMessage());
            log_msg("[{$label}] {$name} — will retry on next startup.");
        }
    }
}

function log_msg(string $msg): void
{
    echo "[migrate] " . $msg . "\n";
}
