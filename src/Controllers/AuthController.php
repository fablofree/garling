<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class AuthController extends Controller
{
    public function showLogin(Request $request, Response $response): void
    {
        if ($this->auth->isLoggedIn()) {
            $this->redirect('/dashboard');
        }

        $this->renderRaw('auth.login', [
            'title'      => 'Login',
            '_csrf_token'=> Session::getCsrfToken(),
            '_flash_error' => Session::getFlash('error'),
        ]);
    }

    public function login(Request $request, Response $response): void
    {
        $this->validateCsrf();

        $username = trim($request->post('username', ''));
        $password = $request->post('password', '');

        if (empty($username) || empty($password)) {
            Session::flash('error', 'Username and password are required.');
            $this->redirect('/login');
        }

        if ($this->auth->attempt($username, $password)) {
            $this->redirect('/dashboard');
        }

        Session::flash('error', 'Invalid username or password.');
        $this->redirect('/login');
    }

    public function logout(Request $request, Response $response): void
    {
        $this->auth->logout();
        $this->redirect('/login');
    }
}
