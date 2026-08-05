<?php

declare(strict_types=1);

// ── Define root path ────────────────────────────────────────
define('ROOT_PATH', dirname(__DIR__));

// ── Autoloader ──────────────────────────────────────────────
spl_autoload_register(function (string $class): void {
    // Namespace: App\ → src/
    if (str_starts_with($class, 'App\\')) {
        $relative = substr($class, 4); // strip "App\"
        $relative = str_replace('\\', DIRECTORY_SEPARATOR, $relative);
        $file     = ROOT_PATH . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $relative . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});

// ── Bootstrap & run ─────────────────────────────────────────
use App\Core\Application;

try {
    $app = new Application();
    $app->run();
} catch (\Throwable $e) {
    $debug = (bool)(getenv('APP_DEBUG') ?: true);
    http_response_code(500);
    if ($debug) {
        echo '<pre style="font-family:monospace;padding:20px;background:#fef2f2;color:#7f1d1d;border:1px solid #fca5a5;">';
        echo '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage(), ENT_QUOTES) . "\n\n";
        echo '<strong>File:</strong> ' . htmlspecialchars($e->getFile(), ENT_QUOTES) . ':' . $e->getLine() . "\n\n";
        echo '<strong>Trace:</strong>' . "\n" . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES);
        echo '</pre>';
    } else {
        echo '<h1>500 — Internal Server Error</h1><p>Something went wrong. Please try again later.</p>';
    }
}
