<?php

declare(strict_types=1);

namespace App\Models;

class Expense extends Model
{
    public function getId(): int
    {
        return (int)($this->attributes['id'] ?? 0);
    }

    public function getExpenseDate(): string
    {
        return (string)($this->attributes['expense_date'] ?? '');
    }

    public function getCategory(): string
    {
        return (string)($this->attributes['category'] ?? '');
    }

    public function getDescription(): string
    {
        return (string)($this->attributes['description'] ?? '');
    }

    public function getAmount(): float
    {
        return (float)($this->attributes['amount'] ?? 0);
    }

    public function getReference(): string
    {
        return (string)($this->attributes['reference'] ?? '');
    }

    public function getNotes(): string
    {
        return (string)($this->attributes['notes'] ?? '');
    }
}
