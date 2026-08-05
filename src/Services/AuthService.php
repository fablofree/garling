<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use App\Models\User;
use App\Repositories\UserRepository;

class AuthService
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    public function attempt(string $username, string $password): bool
    {
        $userData = $this->userRepository->findByUsername($username);

        if ($userData === null) {
            return false;
        }

        $user = new User($userData);

        if (!$user->isActive()) {
            return false;
        }

        if (!$user->verifyPassword($password)) {
            return false;
        }

        // Store user in session
        Session::set('user_id',   $user->getId());
        Session::set('user_name', $user->getFullName() ?: $user->getUsername());
        Session::set('user_role', $user->getRole());
        Session::regenerate();

        return true;
    }

    public function logout(): void
    {
        Session::destroy();
    }

    public function isLoggedIn(): bool
    {
        return Session::has('user_id');
    }

    public function getCurrentUserId(): ?int
    {
        $id = Session::get('user_id');
        return $id !== null ? (int)$id : null;
    }

    public function getCurrentUser(): ?User
    {
        $id = $this->getCurrentUserId();
        if ($id === null) {
            return null;
        }
        $data = $this->userRepository->findById($id);
        return $data ? new User($data) : null;
    }

    public function requireAuth(): void
    {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }
}
