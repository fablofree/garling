<?php

declare(strict_types=1);

namespace App\Models;

class User extends Model
{
    public function getId(): int
    {
        return (int)($this->attributes['id'] ?? 0);
    }

    public function getUsername(): string
    {
        return (string)($this->attributes['username'] ?? '');
    }

    public function getFullName(): string
    {
        return (string)($this->attributes['full_name'] ?? '');
    }

    public function getEmail(): string
    {
        return (string)($this->attributes['email'] ?? '');
    }

    public function getRole(): string
    {
        return (string)($this->attributes['role'] ?? 'staff');
    }

    public function isAdmin(): bool
    {
        return $this->getRole() === 'admin';
    }

    public function isActive(): bool
    {
        $v = $this->attributes['is_active'] ?? true;
        if ($v === 'f' || $v === false || $v === 0 || $v === '0') return false;
        return true;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, (string)($this->attributes['password'] ?? ''));
    }
}
