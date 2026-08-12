<?php

declare(strict_types=1);

namespace App\Repositories;

class QuotationRepository extends BaseRepository
{
    protected string $table = 'quotations';

    public function findByServiceEntryId(int $serviceEntryId): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM quotations WHERE service_entry_id = :id",
            ['id' => $serviceEntryId]
        ) ?: null;
    }

    public function createForEntry(int $serviceEntryId): array
    {
        $this->db->execute(
            "INSERT INTO quotations (quotation_number, service_entry_id, generated_at) VALUES (:tmp, :sid, NOW())",
            ['tmp' => 'PENDING-SE-' . $serviceEntryId, 'sid' => $serviceEntryId]
        );
        $id = (int)$this->db->lastInsertId();
        $number = str_pad((string)$id, 6, '0', STR_PAD_LEFT);
        $this->db->execute(
            "UPDATE quotations SET quotation_number = :num WHERE id = :id",
            ['num' => $number, 'id' => $id]
        );
        return ['id' => $id, 'quotation_number' => $number, 'service_entry_id' => $serviceEntryId];
    }

    public function findAll(): array
    {
        return $this->db->fetchAll(
            "SELECT q.*, se.entry_date, se.total_cost, se.customer_id, se.vehicle_id,
                    c.name AS customer_name, v.registration_no
             FROM quotations q
             JOIN service_entries se ON se.id = q.service_entry_id
             JOIN customers c ON c.id = se.customer_id
             JOIN vehicles v  ON v.id = se.vehicle_id
             ORDER BY q.id DESC"
        );
    }

    public function search(string $q): array
    {
        $like = '%' . $q . '%';
        return $this->db->fetchAll(
            "SELECT q.*, se.entry_date, se.total_cost,
                    c.name AS customer_name, v.registration_no
             FROM quotations q
             JOIN service_entries se ON se.id = q.service_entry_id
             JOIN customers c ON c.id = se.customer_id
             JOIN vehicles v  ON v.id = se.vehicle_id
             WHERE q.quotation_number  LIKE :q
                OR c.name              LIKE :q2
                OR v.registration_no   LIKE :q3",
            ['q' => $like, 'q2' => $like, 'q3' => $like]
        );
    }
}
