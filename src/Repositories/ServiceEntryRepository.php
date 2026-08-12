<?php

declare(strict_types=1);

namespace App\Repositories;

class ServiceEntryRepository extends BaseRepository
{
    protected string $table = 'service_entries';

    public function findAll(): array
    {
        return $this->db->fetchAll(
            "SELECT se.*,
                c.name AS customer_name,
                v.registration_no,
                v.make AS vehicle_make,
                v.model AS vehicle_model,
                COALESCE(p.total_paid, 0) AS total_paid
             FROM service_entries se
             LEFT JOIN customers c ON c.id = se.customer_id
             LEFT JOIN vehicles v ON v.id = se.vehicle_id
             LEFT JOIN (
                 SELECT service_entry_id, SUM(amount) as total_paid
                 FROM payments GROUP BY service_entry_id
             ) p ON p.service_entry_id = se.id
             ORDER BY se.entry_date DESC, se.id DESC"
        );
    }

    public function findById(int $id): ?array
    {
        $row = $this->db->fetchOne(
            "SELECT se.*,
                c.name AS customer_name,
                c.brn AS customer_brn,
                c.vat_number AS customer_vat_number,
                v.registration_no,
                v.make AS vehicle_make,
                v.model AS vehicle_model,
                v.colour AS vehicle_colour,
                v.chassis_no AS vehicle_chassis_no,
                v.distance_unit,
                COALESCE(p.total_paid, 0) AS total_paid
             FROM service_entries se
             LEFT JOIN customers c ON c.id = se.customer_id
             LEFT JOIN vehicles v ON v.id = se.vehicle_id
             LEFT JOIN (
                 SELECT service_entry_id, SUM(amount) as total_paid
                 FROM payments GROUP BY service_entry_id
             ) p ON p.service_entry_id = se.id
             WHERE se.id = :id",
            ['id' => $id]
        );
        return $row ?: null;
    }

    public function findByCustomerId(int $customerId): array
    {
        return $this->db->fetchAll(
            "SELECT se.*,
                v.registration_no,
                v.make AS vehicle_make,
                v.model AS vehicle_model,
                COALESCE(p.total_paid, 0) AS total_paid
             FROM service_entries se
             LEFT JOIN vehicles v ON v.id = se.vehicle_id
             LEFT JOIN (
                 SELECT service_entry_id, SUM(amount) as total_paid
                 FROM payments GROUP BY service_entry_id
             ) p ON p.service_entry_id = se.id
             WHERE se.customer_id = :cid
             ORDER BY se.entry_date DESC",
            ['cid' => $customerId]
        );
    }

    public function findByVehicleId(int $vehicleId): array
    {
        return $this->db->fetchAll(
            "SELECT se.*,
                c.name AS customer_name,
                COALESCE(p.total_paid, 0) AS total_paid
             FROM service_entries se
             LEFT JOIN customers c ON c.id = se.customer_id
             LEFT JOIN (
                 SELECT service_entry_id, SUM(amount) as total_paid
                 FROM payments GROUP BY service_entry_id
             ) p ON p.service_entry_id = se.id
             WHERE se.vehicle_id = :vid
             ORDER BY se.entry_date DESC",
            ['vid' => $vehicleId]
        );
    }

    public function create(array $data): int
    {
        $now = date('Y-m-d H:i:s');
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        $columns      = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $this->db->execute(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})",
            $data
        );
        return (int)$this->db->lastInsertId();
    }

    public function getSpareParts(int $serviceEntryId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM spare_parts WHERE service_entry_id = :id ORDER BY sort_order ASC, id ASC",
            ['id' => $serviceEntryId]
        );
    }

    public function getRepairs(int $serviceEntryId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM repairs WHERE service_entry_id = :id ORDER BY sort_order ASC, id ASC",
            ['id' => $serviceEntryId]
        );
    }

    public function deleteSpareParts(int $serviceEntryId): void
    {
        $this->db->execute(
            "DELETE FROM spare_parts WHERE service_entry_id = :id",
            ['id' => $serviceEntryId]
        );
    }

    public function deleteRepairs(int $serviceEntryId): void
    {
        $this->db->execute(
            "DELETE FROM repairs WHERE service_entry_id = :id",
            ['id' => $serviceEntryId]
        );
    }

    public function insertSparePart(int $serviceEntryId, array $part, int $sortOrder): void
    {
        $this->db->execute(
            "INSERT INTO spare_parts (service_entry_id, description, amount, sort_order)
             VALUES (:service_entry_id, :description, :amount, :sort_order)",
            [
                'service_entry_id' => $serviceEntryId,
                'description'      => $part['description'],
                'amount'           => $part['amount'],
                'sort_order'       => $sortOrder,
            ]
        );
    }

    public function insertRepair(int $serviceEntryId, array $repair, int $sortOrder): void
    {
        $this->db->execute(
            "INSERT INTO repairs (service_entry_id, description, amount, sort_order)
             VALUES (:service_entry_id, :description, :amount, :sort_order)",
            [
                'service_entry_id' => $serviceEntryId,
                'description'      => $repair['description'],
                'amount'           => $repair['amount'],
                'sort_order'       => $sortOrder,
            ]
        );
    }

    public function getRecentEntries(int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT se.*,
                c.name AS customer_name,
                v.registration_no,
                v.make AS vehicle_make,
                COALESCE(p.total_paid, 0) AS total_paid
             FROM service_entries se
             LEFT JOIN customers c ON c.id = se.customer_id
             LEFT JOIN vehicles v ON v.id = se.vehicle_id
             LEFT JOIN (
                 SELECT service_entry_id, SUM(amount) as total_paid
                 FROM payments GROUP BY service_entry_id
             ) p ON p.service_entry_id = se.id
             ORDER BY se.entry_date DESC, se.id DESC
             LIMIT :limit",
            ['limit' => $limit]
        );
    }

    public function getUnpaidInvoices(): array
    {
        return $this->db->fetchAll(
            "SELECT se.*,
                c.name AS customer_name,
                v.registration_no,
                COALESCE(p.total_paid, 0) AS total_paid,
                se.total_cost - COALESCE(p.total_paid, 0) AS balance
             FROM service_entries se
             LEFT JOIN customers c ON c.id = se.customer_id
             LEFT JOIN vehicles v ON v.id = se.vehicle_id
             LEFT JOIN (
                 SELECT service_entry_id, SUM(amount) as total_paid
                 FROM payments GROUP BY service_entry_id
             ) p ON p.service_entry_id = se.id
             WHERE se.is_quotation = 0
               AND se.total_cost > COALESCE(p.total_paid, 0)
             ORDER BY se.entry_date ASC"
        );
    }

    public function getMonthlyStats(int $months = 12): array
    {
        return $this->db->fetchAll(
            "SELECT
                DATE_FORMAT(se.entry_date, '%Y-%m') AS month,
                COUNT(se.id) AS entry_count,
                COALESCE(SUM(se.total_cost), 0) AS total_invoiced,
                COALESCE(SUM(p.paid), 0) AS total_collected
             FROM service_entries se
             LEFT JOIN (
                 SELECT service_entry_id, SUM(amount) as paid
                 FROM payments GROUP BY service_entry_id
             ) p ON p.service_entry_id = se.id
             WHERE se.is_quotation = 0
               AND se.entry_date >= DATE_SUB(CURDATE(), INTERVAL " . (int)$months . " MONTH)
             GROUP BY DATE_FORMAT(se.entry_date, '%Y-%m')
             ORDER BY month DESC"
        );
    }
}
