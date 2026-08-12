<?php

declare(strict_types=1);

namespace App\Repositories;

class CustomerRepository extends BaseRepository
{
    protected string $table = 'customers';

    public function findAll(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM customers ORDER BY name ASC"
        );
    }

    public function search(string $query): array
    {
        $q = '%' . $query . '%';
        return $this->db->fetchAll(
            "SELECT * FROM customers
             WHERE name LIKE :name OR email LIKE :email OR tel_mobile LIKE :tel_mobile
             ORDER BY name ASC",
            [
                'name'      => $q,
                'email'     => $q,
                'tel_mobile' => $q
            ]
        );
    }

    public function countAll(): int
    {
        return (int)($this->db->fetchOne("SELECT COUNT(*) as cnt FROM customers")['cnt'] ?? 0);
    }

    public function findWithDebt(): array
    {
        return $this->db->fetchAll(
            "SELECT
                c.id,
                c.name,
                c.tel_mobile,
                c.email,
                COALESCE(SUM(se.total_cost), 0) AS total_invoiced,
                COALESCE(SUM(p.paid), 0) AS total_paid,
                COALESCE(SUM(se.total_cost), 0) - COALESCE(SUM(p.paid), 0) AS balance
             FROM customers c
             LEFT JOIN service_entries se ON se.customer_id = c.id AND se.is_quotation = 0
             LEFT JOIN (
                 SELECT service_entry_id, SUM(amount) as paid
                 FROM payments GROUP BY service_entry_id
             ) p ON p.service_entry_id = se.id
             GROUP BY c.id, c.name, c.tel_mobile, c.email
             HAVING COALESCE(SUM(se.total_cost), 0) - COALESCE(SUM(p.paid), 0) > 0
             ORDER BY balance DESC"
        );
    }

    public function getTotalOutstandingDebt(): float
    {
        $row = $this->db->fetchOne(
            "SELECT
                COALESCE(SUM(se.total_cost), 0) - COALESCE(SUM(p.paid), 0) AS total_balance
             FROM service_entries se
             LEFT JOIN (
                 SELECT service_entry_id, SUM(amount) as paid
                 FROM payments GROUP BY service_entry_id
             ) p ON p.service_entry_id = se.id
             WHERE se.is_quotation = 0"
        );
        return (float)($row['total_balance'] ?? 0);
    }
}
