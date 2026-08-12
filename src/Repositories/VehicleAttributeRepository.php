<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

class VehicleAttributeRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── Makes ──────────────────────────────────────────────────────────

    public function getMakes(): array
    {
        return $this->db->fetchAll("SELECT * FROM vehicle_makes ORDER BY name");
    }

    public function createMake(string $name): array
    {
        $this->db->execute(
            "INSERT INTO vehicle_makes (name, created_at) VALUES (:name, NOW())",
            ['name' => $name]
        );
        $id = (int)$this->db->lastInsertId();
        return $this->db->fetchOne("SELECT * FROM vehicle_makes WHERE id = :id", ['id' => $id]) ?: [];
    }

    public function updateMake(int $id, string $name): void
    {
        $this->db->execute(
            "UPDATE vehicle_makes SET name = :name WHERE id = :id",
            ['name' => $name, 'id' => $id]
        );
    }

    public function deleteMake(int $id): void
    {
        $this->db->execute("DELETE FROM vehicle_makes WHERE id = :id", ['id' => $id]);
    }

    // ── Models ─────────────────────────────────────────────────────────

    public function getModels(?int $makeId = null): array
    {
        if ($makeId !== null) {
            return $this->db->fetchAll(
                "SELECT vm.*, mk.name AS make_name
                 FROM vehicle_models vm
                 LEFT JOIN vehicle_makes mk ON mk.id = vm.make_id
                 WHERE vm.make_id = :make_id
                 ORDER BY vm.name",
                ['make_id' => $makeId]
            );
        }
        return $this->db->fetchAll(
            "SELECT vm.*, mk.name AS make_name
             FROM vehicle_models vm
             LEFT JOIN vehicle_makes mk ON mk.id = vm.make_id
             ORDER BY mk.name, vm.name"
        );
    }

    public function createModel(string $name, ?int $makeId): array
    {
        $this->db->execute(
            "INSERT INTO vehicle_models (name, make_id, created_at) VALUES (:name, :make_id, NOW())",
            ['name' => $name, 'make_id' => $makeId]
        );
        $id = (int)$this->db->lastInsertId();
        return $this->db->fetchOne("SELECT * FROM vehicle_models WHERE id = :id", ['id' => $id]) ?: [];
    }

    public function updateModel(int $id, string $name, ?int $makeId): void
    {
        $this->db->execute(
            "UPDATE vehicle_models SET name = :name, make_id = :make_id WHERE id = :id",
            ['name' => $name, 'make_id' => $makeId, 'id' => $id]
        );
    }

    public function deleteModel(int $id): void
    {
        $this->db->execute("DELETE FROM vehicle_models WHERE id = :id", ['id' => $id]);
    }

    // ── Types ──────────────────────────────────────────────────────────

    public function getTypes(): array
    {
        return $this->db->fetchAll("SELECT * FROM vehicle_types ORDER BY name");
    }

    public function createType(string $name): array
    {
        $this->db->execute(
            "INSERT INTO vehicle_types (name, created_at) VALUES (:name, NOW())",
            ['name' => $name]
        );
        $id = (int)$this->db->lastInsertId();
        return $this->db->fetchOne("SELECT * FROM vehicle_types WHERE id = :id", ['id' => $id]) ?: [];
    }

    public function updateType(int $id, string $name): void
    {
        $this->db->execute(
            "UPDATE vehicle_types SET name = :name WHERE id = :id",
            ['name' => $name, 'id' => $id]
        );
    }

    public function deleteType(int $id): void
    {
        $this->db->execute("DELETE FROM vehicle_types WHERE id = :id", ['id' => $id]);
    }

    // ── Colours ────────────────────────────────────────────────────────

    public function getColours(): array
    {
        return $this->db->fetchAll("SELECT * FROM vehicle_colours ORDER BY name");
    }

    public function createColour(string $name): array
    {
        $this->db->execute(
            "INSERT INTO vehicle_colours (name, created_at) VALUES (:name, NOW())",
            ['name' => $name]
        );
        $id = (int)$this->db->lastInsertId();
        return $this->db->fetchOne("SELECT * FROM vehicle_colours WHERE id = :id", ['id' => $id]) ?: [];
    }

    public function updateColour(int $id, string $name): void
    {
        $this->db->execute(
            "UPDATE vehicle_colours SET name = :name WHERE id = :id",
            ['name' => $name, 'id' => $id]
        );
    }

    public function deleteColour(int $id): void
    {
        $this->db->execute("DELETE FROM vehicle_colours WHERE id = :id", ['id' => $id]);
    }
}
