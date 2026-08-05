<?php

declare(strict_types=1);

namespace App\Models;

class Vehicle extends Model
{
    public function getId(): int
    {
        return (int)($this->attributes['id'] ?? 0);
    }

    public function getCustomerId(): int
    {
        return (int)($this->attributes['customer_id'] ?? 0);
    }

    public function getRegistrationNo(): string
    {
        return (string)($this->attributes['registration_no'] ?? '');
    }

    public function getChassisNo(): string
    {
        return (string)($this->attributes['chassis_no'] ?? '');
    }

    public function getMake(): string
    {
        return (string)($this->attributes['make'] ?? '');
    }

    public function getModel(): string
    {
        return (string)($this->attributes['model'] ?? '');
    }

    public function getVehicleType(): string
    {
        return (string)($this->attributes['vehicle_type'] ?? '');
    }

    public function getColour(): string
    {
        return (string)($this->attributes['colour'] ?? '');
    }

    public function getYear(): ?int
    {
        return isset($this->attributes['year']) ? (int)$this->attributes['year'] : null;
    }

    public function getDistanceUnit(): string
    {
        return (string)($this->attributes['distance_unit'] ?? 'km');
    }

    public function getServicingFrequency(): int
    {
        return (int)($this->attributes['servicing_frequency'] ?? 5000);
    }

    public function getNotes(): string
    {
        return (string)($this->attributes['notes'] ?? '');
    }

    public function getDisplayName(): string
    {
        $parts = array_filter([
            $this->getMake(),
            $this->getModel(),
            '(' . $this->getRegistrationNo() . ')',
        ]);
        return implode(' ', $parts);
    }

    // For joined queries - customer name
    public function getCustomerName(): string
    {
        return (string)($this->attributes['customer_name'] ?? '');
    }
}
