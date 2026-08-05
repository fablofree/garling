<?php

declare(strict_types=1);

namespace App\Core;

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\CustomerController;
use App\Controllers\VehicleController;
use App\Controllers\ServiceController;
use App\Controllers\PaymentController;
use App\Controllers\ExpenseController;
use App\Controllers\ReportController;

class Application
{
    private Router $router;
    private Request $request;
    private Response $response;

    public function __construct()
    {
        $config = require ROOT_PATH . '/config/app.php';
        date_default_timezone_set($config['timezone']);

        Session::start();

        $this->request  = new Request();
        $this->response = new Response();
        $this->router   = new Router();

        $this->registerRoutes();
    }

    private function registerRoutes(): void
    {
        $r = $this->router;

        // Root
        $r->get('/', [DashboardController::class, 'index']);

        // Auth
        $r->get('/login',  [AuthController::class, 'showLogin'],  'login');
        $r->post('/login', [AuthController::class, 'login']);
        $r->get('/logout', [AuthController::class, 'logout'],     'logout');

        // Dashboard
        $r->get('/dashboard', [DashboardController::class, 'index'], 'dashboard');

        // Customers
        $r->get('/customers',               [CustomerController::class, 'index'],  'customers.index');
        $r->get('/customers/create',        [CustomerController::class, 'create'], 'customers.create');
        $r->post('/customers/store',        [CustomerController::class, 'store'],  'customers.store');
        $r->get('/customers/{id}',          [CustomerController::class, 'show'],   'customers.show');
        $r->get('/customers/{id}/edit',     [CustomerController::class, 'edit'],   'customers.edit');
        $r->post('/customers/{id}/update',  [CustomerController::class, 'update'], 'customers.update');
        $r->post('/customers/{id}/delete',  [CustomerController::class, 'delete'], 'customers.delete');

        // Vehicles
        $r->get('/vehicles',               [VehicleController::class, 'index'],  'vehicles.index');
        $r->get('/vehicles/create',        [VehicleController::class, 'create'], 'vehicles.create');
        $r->post('/vehicles/store',        [VehicleController::class, 'store'],  'vehicles.store');
        $r->get('/vehicles/{id}',          [VehicleController::class, 'show'],   'vehicles.show');
        $r->get('/vehicles/{id}/edit',     [VehicleController::class, 'edit'],   'vehicles.edit');
        $r->post('/vehicles/{id}/update',  [VehicleController::class, 'update'], 'vehicles.update');
        $r->post('/vehicles/{id}/delete',  [VehicleController::class, 'delete'], 'vehicles.delete');

        // Services
        $r->get('/services',               [ServiceController::class, 'index'],   'services.index');
        $r->get('/services/create',        [ServiceController::class, 'create'],  'services.create');
        $r->post('/services/store',        [ServiceController::class, 'store'],   'services.store');
        $r->get('/services/{id}',          [ServiceController::class, 'show'],    'services.show');
        $r->get('/services/{id}/edit',     [ServiceController::class, 'edit'],    'services.edit');
        $r->post('/services/{id}/update',  [ServiceController::class, 'update'],  'services.update');
        $r->post('/services/{id}/delete',  [ServiceController::class, 'delete'],  'services.delete');
        $r->get('/services/{id}/invoice',  [ServiceController::class, 'invoice'], 'services.invoice');

        // Payments
        $r->get('/services/{serviceId}/payments/create',  [PaymentController::class, 'create'], 'payments.create');
        $r->post('/services/{serviceId}/payments/store',  [PaymentController::class, 'store'],  'payments.store');

        // Expenses
        $r->get('/expenses',               [ExpenseController::class, 'index'],  'expenses.index');
        $r->get('/expenses/create',        [ExpenseController::class, 'create'], 'expenses.create');
        $r->post('/expenses/store',        [ExpenseController::class, 'store'],  'expenses.store');
        $r->get('/expenses/{id}/edit',     [ExpenseController::class, 'edit'],   'expenses.edit');
        $r->post('/expenses/{id}/update',  [ExpenseController::class, 'update'], 'expenses.update');
        $r->post('/expenses/{id}/delete',  [ExpenseController::class, 'delete'], 'expenses.delete');

        // Reports
        $r->get('/reports', [ReportController::class, 'index'], 'reports.index');
    }

    public function run(): void
    {
        $this->router->dispatch($this->request, $this->response);
    }
}
