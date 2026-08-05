<?php

declare(strict_types=1);

namespace App\Models;

class ServiceEntry extends Model
{
    public function getId(): int
    {
        return (int)($this->attributes['id'] ?? 0);
    }

    public function getEntryDate(): string
    {
        return (string)($this->attributes['entry_date'] ?? '');
    }

    public function getVehicleId(): int
    {
        return (int)($this->attributes['vehicle_id'] ?? 0);
    }

    public function getCustomerId(): int
    {
        return (int)($this->attributes['customer_id'] ?? 0);
    }

    public function getOdometer(): ?int
    {
        return isset($this->attributes['odometer']) ? (int)$this->attributes['odometer'] : null;
    }

    public function getNextServicing(): ?int
    {
        return isset($this->attributes['next_servicing']) ? (int)$this->attributes['next_servicing'] : null;
    }

    public function getRemarks(): string
    {
        return (string)($this->attributes['remarks'] ?? '');
    }

    public function getEntryType(): string
    {
        return (string)($this->attributes['entry_type'] ?? 'INVOICE');
    }

    public function isQuotation(): bool
    {
        $v = $this->attributes['is_quotation'] ?? false;
        // PostgreSQL PDO returns 't'/'f' for booleans
        if ($v === 't' || $v === true || $v === 1 || $v === '1') return true;
        return false;
    }

    public function isCompleted(): bool
    {
        $v = $this->attributes['is_completed'] ?? false;
        if ($v === 't' || $v === true || $v === 1 || $v === '1') return true;
        return false;
    }

    public function getDeliveryDate(): ?string
    {
        return $this->attributes['delivery_date'] ?? null;
    }

    public function getVatPercent(): float
    {
        return (float)($this->attributes['vat_percent'] ?? 0);
    }

    public function getDiscountAmount(): float
    {
        return (float)($this->attributes['discount_amount'] ?? 0);
    }

    public function getTotalParts(): float
    {
        return (float)($this->attributes['total_parts'] ?? 0);
    }

    public function getTotalLabour(): float
    {
        return (float)($this->attributes['total_labour'] ?? 0);
    }

    public function getSubtotal(): float
    {
        return (float)($this->attributes['subtotal'] ?? 0);
    }

    public function getVatAmount(): float
    {
        return (float)($this->attributes['vat_amount'] ?? 0);
    }

    public function getTotalCost(): float
    {
        return (float)($this->attributes['total_cost'] ?? 0);
    }

    public function getTotalPaid(): float
    {
        return (float)($this->attributes['total_paid'] ?? 0);
    }

    public function getBalance(): float
    {
        return $this->getTotalCost() - $this->getTotalPaid();
    }

    public function isPaid(): bool
    {
        return $this->getBalance() <= 0;
    }

    public function isPartiallyPaid(): bool
    {
        $paid = $this->getTotalPaid();
        return $paid > 0 && $paid < $this->getTotalCost();
    }

    public function getPaymentStatus(): string
    {
        if ($this->isPaid()) {
            return 'paid';
        }
        if ($this->isPartiallyPaid()) {
            return 'partial';
        }
        return 'unpaid';
    }

    // Joined fields
    public function getCustomerName(): string
    {
        return (string)($this->attributes['customer_name'] ?? '');
    }

    public function getRegistrationNo(): string
    {
        return (string)($this->attributes['registration_no'] ?? '');
    }

    public function getVehicleMake(): string
    {
        return (string)($this->attributes['vehicle_make'] ?? '');
    }

    public function getVehicleModel(): string
    {
        return (string)($this->attributes['vehicle_model'] ?? '');
    }
}
