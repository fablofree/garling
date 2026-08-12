<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

class CatalogRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── Categories ─────────────────────────────────────────────────────

    public function getCategories(): array
    {
        return $this->db->fetchAll(
            "SELECT pc.*, COUNT(ci.id) AS item_count
             FROM part_categories pc
             LEFT JOIN catalog_items ci ON ci.category_id = pc.id
             GROUP BY pc.id
             ORDER BY pc.name"
        );
    }

    public function getCategoryById(int $id): ?array
    {
        $row = $this->db->fetchOne(
            "SELECT * FROM part_categories WHERE id = :id",
            ['id' => $id]
        );
        return $row ?: null;
    }

    public function createCategory(string $name): int
    {
        $this->db->execute(
            "INSERT INTO part_categories (name, created_at) VALUES (:name, NOW())",
            ['name' => $name]
        );
        return (int)$this->db->lastInsertId();
    }

    public function updateCategory(int $id, string $name): void
    {
        $this->db->execute(
            "UPDATE part_categories SET name = :name WHERE id = :id",
            ['name' => $name, 'id' => $id]
        );
    }

    public function deleteCategory(int $id): void
    {
        $this->db->execute("DELETE FROM part_categories WHERE id = :id", ['id' => $id]);
    }

    // ── Items ──────────────────────────────────────────────────────────

    public function getAllItems(): array
    {
        return $this->db->fetchAll(
            "SELECT ci.*, pc.name AS category_name
             FROM catalog_items ci
             JOIN part_categories pc ON pc.id = ci.category_id
             ORDER BY pc.name, ci.name"
        );
    }

    public function getItemsByCategory(int $categoryId): array
    {
        return $this->db->fetchAll(
            "SELECT ci.*, pc.name AS category_name
             FROM catalog_items ci
             JOIN part_categories pc ON pc.id = ci.category_id
             WHERE ci.category_id = :cat_id
             ORDER BY ci.name",
            ['cat_id' => $categoryId]
        );
    }

    public function getItemById(int $id): ?array
    {
        $row = $this->db->fetchOne(
            "SELECT ci.*, pc.name AS category_name
             FROM catalog_items ci
             JOIN part_categories pc ON pc.id = ci.category_id
             WHERE ci.id = :id",
            ['id' => $id]
        );
        return $row ?: null;
    }

    public function createItem(array $data): int
    {
        $this->db->execute(
            "INSERT INTO catalog_items (category_id, name, description, unit_price, created_at, updated_at)
             VALUES (:category_id, :name, :description, :unit_price, NOW(), NOW())",
            [
                'category_id' => (int)$data['category_id'],
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'unit_price'  => (float)($data['unit_price'] ?? 0),
            ]
        );
        return (int)$this->db->lastInsertId();
    }

    public function updateItem(int $id, array $data): void
    {
        $this->db->execute(
            "UPDATE catalog_items
             SET category_id = :category_id,
                 name        = :name,
                 description = :description,
                 unit_price  = :unit_price,
                 updated_at  = NOW()
             WHERE id = :id",
            [
                'category_id' => (int)$data['category_id'],
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'unit_price'  => (float)($data['unit_price'] ?? 0),
                'id'          => $id,
            ]
        );
    }

    public function deleteItem(int $id): void
    {
        $this->db->execute("DELETE FROM catalog_items WHERE id = :id", ['id' => $id]);
    }

    /**
     * LIKE search on item name (case-insensitive via MySQL default collation).
     */
    public function searchItems(string $q): array
    {
        return $this->db->fetchAll(
            "SELECT ci.id, ci.name, ci.unit_price, pc.name AS category_name
             FROM catalog_items ci
             JOIN part_categories pc ON pc.id = ci.category_id
             WHERE ci.name LIKE :q OR ci.description LIKE :q2
             ORDER BY ci.name
             LIMIT 30",
            ['q' => '%' . $q . '%', 'q2' => '%' . $q . '%']
        );
    }
}
