<?php

declare(strict_types=1);

namespace App\Repositories;

class ExpenseRepository extends BaseRepository
{
    protected string $table = 'expenses';

    public function findAll(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM expenses ORDER BY expense_date DESC, id DESC"
        );
    }

    public function findByMonth(int $year, int $month): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM expenses
             WHERE EXTRACT(YEAR FROM expense_date) = :year
               AND EXTRACT(MONTH FROM expense_date) = :month
             ORDER BY expense_date DESC",
            ['year' => $year, 'month' => $month]
        );
    }

    public function getMonthlyTotal(int $year, int $month): float
    {
        $row = $this->db->fetchOne(
            "SELECT COALESCE(SUM(amount), 0) AS total FROM expenses
             WHERE EXTRACT(YEAR FROM expense_date) = :year
               AND EXTRACT(MONTH FROM expense_date) = :month",
            ['year' => $year, 'month' => $month]
        );
        return (float)($row['total'] ?? 0);
    }

    public function getTotalByCategory(int $year, int $month): array
    {
        return $this->db->fetchAll(
            "SELECT category, COALESCE(SUM(amount), 0) AS total
             FROM expenses
             WHERE EXTRACT(YEAR FROM expense_date) = :year
               AND EXTRACT(MONTH FROM expense_date) = :month
             GROUP BY category
             ORDER BY total DESC",
            ['year' => $year, 'month' => $month]
        );
    }
}
