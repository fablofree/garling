<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\UserRepository;

class UserController extends Controller
{
    private UserRepository $userRepo;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->userRepo = new UserRepository();
    }

    /** GET /users */
    public function index(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        $this->render('users.index', [
            'title'      => 'User Management',
            'activeMenu' => 'users',
            'users'      => $this->userRepo->findAll(),
        ]);
    }

    /** GET /users/create */
    public function create(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        $this->render('users.form', [
            'title'       => 'New User',
            'activeMenu'  => 'users',
            'user'        => null,
            'action'      => '/users/store',
            'submitLabel' => 'Create User',
        ]);
    }

    /** POST /users/store */
    public function store(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->requireAdmin();
        $this->validateCsrf();

        $username = trim($request->post('username', ''));
        $password = $request->post('password', '');
        $fullName = trim($request->post('full_name', ''));
        $email    = trim($request->post('email', ''));
        $role     = in_array($request->post('role'), ['admin', 'staff'], true)
            ? $request->post('role')
            : 'staff';

        $errors = [];
        if (!$username) {
            $errors[] = 'Username is required.';
        }
        if (!$password) {
            $errors[] = 'Password is required.';
        }
        if (!$fullName) {
            $errors[] = 'Full name is required.';
        }

        if ($errors) {
            Session::flash('error', implode('<br>', $errors));
            $this->redirect('/users/create');
        }

        // Check username uniqueness
        if ($this->userRepo->findByUsername($username)) {
            Session::flash('error', 'Username already exists.');
            $this->redirect('/users/create');
        }

        try {
            $this->userRepo->createUser($username, $password, $fullName, $email, $role);
            Session::flash('success', 'User created successfully.');
            $this->redirect('/users');
        } catch (\Throwable $e) {
            Session::flash('error', 'Error: ' . $e->getMessage());
            $this->redirect('/users/create');
        }
    }

    /** GET /users/{id}/edit */
    public function edit(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        $id   = (int)$request->param('id');
        $user = $this->userRepo->findById($id);

        if (!$user) {
            Session::flash('error', 'User not found.');
            $this->redirect('/users');
        }

        $this->render('users.form', [
            'title'       => 'Edit User: ' . htmlspecialchars($user['username']),
            'activeMenu'  => 'users',
            'user'        => $user,
            'action'      => '/users/' . $id . '/update',
            'submitLabel' => 'Update User',
        ]);
    }

    /** POST /users/{id}/update */
    public function update(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->requireAdmin();
        $this->validateCsrf();

        $id   = (int)$request->param('id');
        $user = $this->userRepo->findById($id);

        if (!$user) {
            Session::flash('error', 'User not found.');
            $this->redirect('/users');
        }

        $fullName = trim($request->post('full_name', ''));
        $email    = trim($request->post('email', ''));
        $role     = in_array($request->post('role'), ['admin', 'staff'], true)
            ? $request->post('role')
            : 'staff';
        $isActive = $request->post('is_active') ? true : false;

        $updateData = [
            'full_name' => $fullName,
            'email'     => $email,
            'role'      => $role,
            'is_active' => $isActive,
        ];

        // Password change (optional)
        $newPassword = $request->post('password', '');
        if ($newPassword !== '') {
            $updateData['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        $this->userRepo->update($id, $updateData);
        Session::flash('success', 'User updated successfully.');
        $this->redirect('/users');
    }

    /** POST /users/{id}/delete */
    public function delete(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->requireAdmin();
        $this->validateCsrf();

        $id = (int)$request->param('id');

        // Cannot delete own account
        if ($id === (int)Session::get('user_id')) {
            Session::flash('error', 'You cannot delete your own account.');
            $this->redirect('/users');
        }

        $this->userRepo->delete($id);
        Session::flash('success', 'User deleted.');
        $this->redirect('/users');
    }

    private function requireAdmin(): void
    {
        if (Session::get('user_role') !== 'admin') {
            Session::flash('error', 'Access denied. Admin only.');
            $this->redirect('/dashboard');
        }
    }
}
