<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\SettingsRepository;

class SettingsController extends Controller
{
    private SettingsRepository $settingsRepo;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->settingsRepo = new SettingsRepository();
    }

    /** GET /settings */
    public function index(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        $settings = $this->settingsRepo->getAll();

        $this->render('settings.index', [
            'title'      => 'App Settings',
            'activeMenu' => 'settings',
            'settings'   => $settings,
        ]);
    }

    /** POST /settings/update */
    public function update(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->requireAdmin();
        $this->validateCsrf();

        $fields = [
            'app_name',
            'currency_symbol',
            'vat_default',
            'app_brn',
            'app_vat_reg',
            'app_address',
            'app_tel',
            'app_email',
        ];
        foreach ($fields as $key) {
            $value = trim($request->post($key, ''));
            $this->settingsRepo->set($key, $value);
        }

        Session::flash('success', 'Settings saved successfully.');
        $this->redirect('/settings');
    }

    /** POST /settings/logo */
    public function uploadLogo(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->requireAdmin();
        $this->validateCsrf();

        $file = $_FILES['logo'] ?? null;

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'No file uploaded or upload error occurred.');
            $this->redirect('/settings');
        }

        $allowed   = ['image/png', 'image/jpeg', 'image/svg+xml'];
        $mimeType  = mime_content_type($file['tmp_name']);
        if (!in_array($mimeType, $allowed, true)) {
            Session::flash('error', 'Invalid file type. Allowed: PNG, JPG, SVG.');
            $this->redirect('/settings');
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            Session::flash('error', 'File too large. Maximum size is 2 MB.');
            $this->redirect('/settings');
        }

        $ext = match ($mimeType) {
            'image/png'     => 'png',
            'image/jpeg'    => 'jpg',
            'image/svg+xml' => 'svg',
            default         => 'png',
        };

        $destRelative = 'public/assets/images/logo-custom.' . $ext;
        $destAbsolute = ROOT_PATH . '/' . $destRelative;

        if (!move_uploaded_file($file['tmp_name'], $destAbsolute)) {
            Session::flash('error', 'Failed to save uploaded file.');
            $this->redirect('/settings');
        }

        $this->settingsRepo->set('app_logo', 'assets/images/logo-custom.' . $ext);

        Session::flash('success', 'Logo updated successfully.');
        $this->redirect('/settings');
    }

    private function requireAdmin(): void
    {
        if (Session::get('user_role') !== 'admin') {
            Session::flash('error', 'Access denied. Admin only.');
            $this->redirect('/dashboard');
        }
    }
}
