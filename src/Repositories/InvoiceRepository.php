<?php

declare(strict_types=1);

namespace App\Repositories;

class InvoiceRepository extends BaseRepository
{
    protected string $table = 'invoices';

    public function findByServiceEntryId(int $serviceEntryId): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM invoices WHERE service_entry_id = :id",
            ['id' => $serviceEntryId]
        ) ?: null;
    }

    public function createForEntry(int $serviceEntryId): array
    {
        $this->db->execute(
            "INSERT INTO invoices (invoice_number, service_entry_id, generated_at) VALUES (:tmp, :sid, NOW())",
            ['tmp' => 'PENDING-SE-' . $serviceEntryId, 'sid' => $serviceEntryId]
        );
        $id = (int)$this->db->lastInsertId();
        $number = str_pad((string)$id, 6, '0', STR_PAD_LEFT);
        $this->db->execute(
            "UPDATE invoices SET invoice_number = :num WHERE id = :id",
            ['num' => $number, 'id' => $id]
        );
        return ['id' => $id, 'invoice_number' => $number, 'service_entry_id' => $serviceEntryId];
    }

    public function findAll(): array
    {
        return $this->db->fetchAll(
            "SELECT i.*, se.entry_date, se.total_cost, se.customer_id, se.vehicle_id,
                    c.name AS customer_name, v.registration_no,
                    v.make AS vehicle_make, v.model AS vehicle_model,
                    COALESCE(p.total_paid, 0) AS total_paid,
                    se.total_cost - COALESCE(p.total_paid, 0) AS balance
             FROM invoices i
             JOIN service_entries se ON se.id = i.service_entry_id
             JOIN customers c ON c.id = se.customer_id
             JOIN vehicles v  ON v.id = se.vehicle_id
             LEFT JOIN (
                 SELECT service_entry_id, SUM(amount) AS total_paid
                 FROM payments GROUP BY service_entry_id
             ) p ON p.service_entry_id = se.id
             ORDER BY i.id DESC"
        );
    }

    public function search(string $q): array
    {
        $like = '%' . $q . '%';
        return $this->db->fetchAll(
            "SELECT i.*, se.entry_date, se.total_cost,
                    c.name AS customer_name, v.registration_no,
                    COALESCE(p.total_paid, 0) AS total_paid,
                    se.total_cost - COALESCE(p.total_paid, 0) AS balance
             FROM invoices i
             JOIN service_entries se ON se.id = i.service_entry_id
             JOIN customers c ON c.id = se.customer_id
             JOIN vehicles v  ON v.id = se.vehicle_id
             LEFT JOIN (
                 SELECT service_entry_id, SUM(amount) AS total_paid
                 FROM payments GROUP BY service_entry_id
             ) p ON p.service_entry_id = se.id
             WHERE i.invoice_number LIKE :q
                OR c.name            LIKE :q2
                OR v.registration_no LIKE :q3",
            ['q' => $like, 'q2' => $like, 'q3' => $like]
        );
    }
}
