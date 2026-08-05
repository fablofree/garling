<?php

declare(strict_types=1);

namespace App\Core;

class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (static::$started || session_status() === PHP_SESSION_ACTIVE) {
            static::$started = true;
            return;
        }

        $config = require ROOT_PATH . '/config/app.php';

        session_name($config['session']['name']);
        session_set_cookie_params([
            'lifetime' => $config['session']['lifetime'],
            'path'     => '/',
            'secure'   => false,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
        static::$started = true;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public static function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }

    public static function generateCsrfToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['_csrf_token'] = $token;
        return $token;
    }

    public static function getCsrfToken(): string
    {
        if (!isset($_SESSION['_csrf_token'])) {
            return static::generateCsrfToken();
        }
        return $_SESSION['_csrf_token'];
    }

    public static function validateCsrfToken(string $token): bool
    {
        $stored = $_SESSION['_csrf_token'] ?? '';
        return hash_equals($stored, $token);
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        session_destroy();
        static::$started = false;
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }
}
