<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

class SettingsRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Return all settings as ['key' => 'value', ...].
     */
    public function getAll(): array
    {
        $rows   = $this->db->fetchAll("SELECT `key`, `value` FROM app_settings ORDER BY `key`");
        $result = [];
        foreach ($rows as $row) {
            $result[$row['key']] = (string)($row['value'] ?? '');
        }
        return $result;
    }

    /**
     * Get a single setting value.
     */
    public function get(string $key, string $default = ''): string
    {
        $row = $this->db->fetchOne(
            "SELECT `value` FROM app_settings WHERE `key` = :key",
            ['key' => $key]
        );
        return $row ? (string)($row['value'] ?? $default) : $default;
    }

    /**
     * Upsert a setting value.
     */
    public function set(string $key, string $value): void
    {
        $this->db->execute(
            "INSERT INTO app_settings (`key`, `value`, updated_at)
             VALUES (:key, :value, NOW())
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), updated_at = NOW()",
            ['key' => $key, 'value' => $value]
        );
    }
}
