<?php

declare(strict_types=1);

namespace App\Models;

class Customer extends Model
{
    public function getId(): int
    {
        return (int)($this->attributes['id'] ?? 0);
    }

    public function getName(): string
    {
        return (string)($this->attributes['name'] ?? '');
    }

    public function getAddress(): string
    {
        return (string)($this->attributes['address'] ?? '');
    }

    public function getTelHome(): string
    {
        return (string)($this->attributes['tel_home'] ?? '');
    }

    public function getTelOffice(): string
    {
        return (string)($this->attributes['tel_office'] ?? '');
    }

    public function getTelMobile(): string
    {
        return (string)($this->attributes['tel_mobile'] ?? '');
    }

    public function getFax(): string
    {
        return (string)($this->attributes['fax'] ?? '');
    }

    public function getEmail(): string
    {
        return (string)($this->attributes['email'] ?? '');
    }

    public function getNotes(): string
    {
        return (string)($this->attributes['notes'] ?? '');
    }

    public function getCreatedAt(): string
    {
        return (string)($this->attributes['created_at'] ?? '');
    }

    public function getPrimaryPhone(): string
    {
        return $this->getTelMobile() ?: $this->getTelHome() ?: $this->getTelOffice();
    }
}
