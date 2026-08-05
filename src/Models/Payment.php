<?php

declare(strict_types=1);

namespace App\Models;

class Payment extends Model
{
    public function getId(): int
    {
        return (int)($this->attributes['id'] ?? 0);
    }

    public function getServiceEntryId(): int
    {
        return (int)($this->attributes['service_entry_id'] ?? 0);
    }

    public function getPaymentDate(): string
    {
        return (string)($this->attributes['payment_date'] ?? '');
    }

    public function getAmount(): float
    {
        return (float)($this->attributes['amount'] ?? 0);
    }

    public function getPaymentMethod(): string
    {
        return (string)($this->attributes['payment_method'] ?? 'CASH');
    }

    public function getChequeNumber(): string
    {
        return (string)($this->attributes['cheque_number'] ?? '');
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
