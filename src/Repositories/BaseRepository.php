<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

abstract class BaseRepository implements RepositoryInterface
{
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findAll(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} ORDER BY {$this->primaryKey} DESC"
        );
    }

    public function findById(int $id): ?array
    {
        $row = $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id",
            ['id' => $id]
        );
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $data = $this->addTimestamps($data, 'create');
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $this->db->execute(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})",
            $data
        );
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $data = $this->addTimestamps($data, 'update');
        $set = implode(', ', array_map(fn($k) => "{$k} = :{$k}", array_keys($data)));
        $data[$this->primaryKey] = $id;
        $affected = $this->db->execute(
            "UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = :{$this->primaryKey}",
            $data
        );
        return $affected > 0;
    }

    public function delete(int $id): bool
    {
        $affected = $this->db->execute(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id",
            ['id' => $id]
        );
        return $affected > 0;
    }

    protected function addTimestamps(array $data, string $type): array
    {
        $now = date('Y-m-d H:i:s');
        if ($type === 'create') {
            $data['created_at'] = $now;
        }
        $data['updated_at'] = $now;
        return $data;
    }

    protected function paginate(string $sql, array $params, int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $countSql = "SELECT COUNT(*) as total FROM ({$sql}) AS counted";
        $total = (int)($this->db->fetchOne($countSql, $params)['total'] ?? 0);

        $items = $this->db->fetchAll("{$sql} LIMIT {$perPage} OFFSET {$offset}", $params);

        return [
            'items'       => $items,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => (int)ceil($total / $perPage),
        ];
    }
}
