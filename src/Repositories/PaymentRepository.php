<?php

declare(strict_types=1);

namespace App\Repositories;

class PaymentRepository extends BaseRepository
{
    protected string $table = 'payments';

    public function findByServiceEntryId(int $serviceEntryId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM payments WHERE service_entry_id = :id ORDER BY payment_date ASC, id ASC",
            ['id' => $serviceEntryId]
        );
    }

    public function getTotalPaidForEntry(int $serviceEntryId): float
    {
        $row = $this->db->fetchOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE service_entry_id = :id",
            ['id' => $serviceEntryId]
        );
        return (float)($row['total'] ?? 0);
    }

    public function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $columns      = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $this->db->execute(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})",
            $data
        );
        return (int)$this->db->lastInsertId();
    }

    public function getDailyRevenue(string $date): float
    {
        $row = $this->db->fetchOne(
            "SELECT COALESCE(SUM(p.amount), 0) as revenue
             FROM payments p
             JOIN service_entries se ON se.id = p.service_entry_id
             WHERE p.payment_date = :date AND se.is_quotation = FALSE",
            ['date' => $date]
        );
        return (float)($row['revenue'] ?? 0);
    }

    public function getWeeklyRevenue(): float
    {
        $row = $this->db->fetchOne(
            "SELECT COALESCE(SUM(p.amount), 0) as revenue
             FROM payments p
             JOIN service_entries se ON se.id = p.service_entry_id
             WHERE p.payment_date >= CURRENT_DATE - INTERVAL '7 days'
               AND se.is_quotation = FALSE"
        );
        return (float)($row['revenue'] ?? 0);
    }
}
