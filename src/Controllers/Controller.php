<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\AuthService;
use App\Repositories\UserRepository;

abstract class Controller
{
    protected Request $request;
    protected Response $response;
    protected AuthService $auth;

    public function __construct(Request $request, Response $response)
    {
        $this->request  = $request;
        $this->response = $response;
        $this->auth     = new AuthService(new UserRepository());
    }

    protected function requireAuth(): void
    {
        if (!$this->auth->isLoggedIn()) {
            $this->response->redirect('/login');
        }
    }

    protected function render(string $view, array $data = []): void
    {
        $config = require ROOT_PATH . '/config/app.php';
        $data['_app']    = $config;
        $data['_user']   = [
            'id'   => Session::get('user_id'),
            'name' => Session::get('user_name'),
            'role' => Session::get('user_role'),
        ];
        $data['_flash_success'] = Session::getFlash('success');
        $data['_flash_error']   = Session::getFlash('error');
        $data['_flash_warning'] = Session::getFlash('warning');
        $data['_csrf_token']    = Session::getCsrfToken();

        // Load app settings from DB (wraps in try/catch so missing table doesn't break app)
        try {
            $settingsRepo = new \App\Repositories\SettingsRepository();
            $dbSettings   = $settingsRepo->getAll();
            $data['_app']['name']               = $dbSettings['app_name']        ?? $data['_app']['name'];
            $data['_app']['logo_url']           = !empty($dbSettings['app_logo'])
                ? '/' . ltrim($dbSettings['app_logo'], '/')
                : '/assets/images/logo.svg';
            $data['_app']['currency']['symbol'] = $dbSettings['currency_symbol'] ?? $data['_app']['currency']['symbol'];
            $data['_app']['vat_default']        = $dbSettings['vat_default']     ?? '0';
            $data['_app']['brn']                = $dbSettings['app_brn']         ?? '';
            $data['_app']['vat_reg']            = $dbSettings['app_vat_reg']     ?? '';
            $data['_app']['address']            = $dbSettings['app_address']     ?? '';
            $data['_app']['tel']                = $dbSettings['app_tel']         ?? '';
            $data['_app']['email']              = $dbSettings['app_email']       ?? '';
        } catch (\Throwable $e) {
            $data['_app']['logo_url']    = '/assets/images/logo.svg';
            $data['_app']['vat_default'] = '0';
            $data['_app']['brn']         = '';
            $data['_app']['vat_reg']     = '';
            $data['_app']['address']     = '';
            $data['_app']['tel']         = '';
            $data['_app']['email']       = '';
        }

        $viewPath = ROOT_PATH . '/src/Views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View not found: {$viewPath}");
        }

        // Render inner view into a buffer, then pass $content to the layout
        ob_start();
        (static function (array $__data, string $__viewPath): void {
            extract($__data, EXTR_SKIP);
            require $__viewPath;
        })($data, $viewPath);
        $data['_content'] = ob_get_clean();

        // Render layout
        $layoutPath = ROOT_PATH . '/src/Views/layout/base.php';
        (static function (array $__data, string $__layoutPath): void {
            extract($__data, EXTR_SKIP);
            require $__layoutPath;
        })($data, $layoutPath);
    }

    protected function renderRaw(string $view, array $data = []): void
    {
        $config = require ROOT_PATH . '/config/app.php';
        $data['_app']        = $config;
        $data['_csrf_token'] = Session::getCsrfToken();

        // Load app settings for raw views (invoices/quotations need company info)
        try {
            $settingsRepo = new \App\Repositories\SettingsRepository();
            $dbSettings   = $settingsRepo->getAll();
            $data['_app']['name']               = $dbSettings['app_name']        ?? $data['_app']['name'];
            $data['_app']['logo_url']           = !empty($dbSettings['app_logo'])
                ? '/' . ltrim($dbSettings['app_logo'], '/')
                : '/assets/images/logo.svg';
            $data['_app']['currency']['symbol'] = $dbSettings['currency_symbol'] ?? $data['_app']['currency']['symbol'];
            $data['_app']['brn']                = $dbSettings['app_brn']         ?? '';
            $data['_app']['vat_reg']            = $dbSettings['app_vat_reg']     ?? '';
            $data['_app']['address']            = $dbSettings['app_address']     ?? '';
            $data['_app']['tel']                = $dbSettings['app_tel']         ?? '';
            $data['_app']['email']              = $dbSettings['app_email']       ?? '';
        } catch (\Throwable $e) {
            $data['_app']['logo_url'] = '/assets/images/logo.svg';
            $data['_app']['brn']      = '';
            $data['_app']['vat_reg']  = '';
            $data['_app']['address']  = '';
            $data['_app']['tel']      = '';
            $data['_app']['email']    = '';
        }

        $viewPath = ROOT_PATH . '/src/Views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View not found: {$viewPath}");
        }

        (static function (array $__data, string $__viewPath): void {
            extract($__data, EXTR_SKIP);
            require $__viewPath;
        })($data, $viewPath);
    }

    protected function validateCsrf(): void
    {
        $token = $this->request->post('_csrf_token', '');
        if (!Session::validateCsrfToken($token)) {
            Session::flash('error', 'Invalid security token. Please try again.');
            $this->response->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }
    }

    protected function e(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    protected function money(float $amount): string
    {
        $config = require ROOT_PATH . '/config/app.php';
        return $config['currency']['symbol'] . ' ' . number_format(
            $amount,
            $config['currency']['decimals'],
            $config['currency']['decimal'],
            $config['currency']['separator']
        );
    }

    protected function redirect(string $url): never
    {
        $this->response->redirect($url);
    }

    protected function back(): never
    {
        $this->response->redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }
}
