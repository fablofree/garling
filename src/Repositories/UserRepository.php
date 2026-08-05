<?php

declare(strict_types=1);

namespace App\Repositories;

class UserRepository extends BaseRepository
{
    protected string $table = 'users';

    public function findByUsername(string $username): ?array
    {
        $row = $this->db->fetchOne(
            "SELECT * FROM users WHERE username = :username",
            ['username' => $username]
        );
        return $row ?: null;
    }

    public function createUser(string $username, string $password, string $fullName, string $email, string $role = 'staff'): int
    {
        return $this->create([
            'username'  => $username,
            'password'  => password_hash($password, PASSWORD_BCRYPT),
            'full_name' => $fullName,
            'email'     => $email,
            'role'      => $role,
            'is_active' => true,
        ]);
    }

    public function updatePassword(int $id, string $newPassword): bool
    {
        return $this->update($id, [
            'password' => password_hash($newPassword, PASSWORD_BCRYPT),
        ]);
    }
}
