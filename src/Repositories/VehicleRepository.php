<?php

declare(strict_types=1);

namespace App\Repositories;

class VehicleRepository extends BaseRepository
{
    protected string $table = 'vehicles';

    public function findAll(): array
    {
        return $this->db->fetchAll(
            "SELECT v.*, c.name AS customer_name
             FROM vehicles v
             LEFT JOIN customers c ON c.id = v.customer_id
             ORDER BY v.registration_no ASC"
        );
    }

    public function findById(int $id): ?array
    {
        $row = $this->db->fetchOne(
            "SELECT v.*, c.name AS customer_name
             FROM vehicles v
             LEFT JOIN customers c ON c.id = v.customer_id
             WHERE v.id = :id",
            ['id' => $id]
        );
        return $row ?: null;
    }

    public function findByCustomerId(int $customerId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM vehicles WHERE customer_id = :cid ORDER BY registration_no ASC",
            ['cid' => $customerId]
        );
    }

    public function search(string $query): array
    {
        $q = '%' . $query . '%';
        return $this->db->fetchAll(
            "SELECT v.*, c.name AS customer_name
             FROM vehicles v
             LEFT JOIN customers c ON c.id = v.customer_id
             WHERE v.registration_no LIKE :q OR v.make LIKE :q OR v.model LIKE :q OR v.chassis_no LIKE :q
             ORDER BY v.registration_no ASC",
            ['q' => $q]
        );
    }

    public function countAll(): int
    {
        return (int)($this->db->fetchOne("SELECT COUNT(*) as cnt FROM vehicles")['cnt'] ?? 0);
    }
}
