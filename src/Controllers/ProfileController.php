<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\UserRepository;

class ProfileController extends Controller
{
    private UserRepository $userRepo;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->userRepo = new UserRepository();
    }

    /** GET /profile */
    public function index(Request $request, Response $response): void
    {
        $this->requireAuth();

        $userId = (int)Session::get('user_id');
        $user   = $this->userRepo->findById($userId);

        if (!$user) {
            $this->redirect('/logout');
        }

        $this->render('profile.index', [
            'title'      => 'My Profile',
            'activeMenu' => 'profile',
            'user'       => $user,
        ]);
    }

    /** POST /profile/update */
    public function update(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $userId   = (int)Session::get('user_id');
        $user     = $this->userRepo->findById($userId);

        if (!$user) {
            $this->redirect('/logout');
        }

        $fullName = trim($request->post('full_name', ''));
        $email    = trim($request->post('email', ''));

        $errors = [];
        if (!$fullName) {
            $errors[] = 'Full name is required.';
        }

        // Password change
        $currentPassword = $request->post('current_password', '');
        $newPassword     = $request->post('new_password', '');
        $confirmPassword = $request->post('confirm_password', '');

        if ($newPassword !== '') {
            if (!password_verify($currentPassword, $user['password'])) {
                $errors[] = 'Current password is incorrect.';
            } elseif (strlen($newPassword) < 6) {
                $errors[] = 'New password must be at least 6 characters.';
            } elseif ($newPassword !== $confirmPassword) {
                $errors[] = 'New password and confirmation do not match.';
            }
        }

        if ($errors) {
            Session::flash('error', implode('<br>', $errors));
            $this->redirect('/profile');
        }

        $updateData = [
            'full_name' => $fullName,
            'email'     => $email,
        ];

        if ($newPassword !== '' && !$errors) {
            $updateData['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        $this->userRepo->update($userId, $updateData);

        // Update session name
        Session::set('user_name', $fullName);

        Session::flash('success', 'Profile updated successfully.');
        $this->redirect('/profile');
    }
}
