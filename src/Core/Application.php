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
use App\Controllers\SettingsController;
use App\Controllers\VehicleAttributeController;
use App\Controllers\CatalogController;
use App\Controllers\UserController;
use App\Controllers\ProfileController;
use App\Controllers\InvoicesController;

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

        // Invoices list
        $r->get('/invoices', [InvoicesController::class, 'index'], 'invoices.index');

        // Reports
        $r->get('/reports', [ReportController::class, 'index'], 'reports.index');

        // Settings (admin)
        $r->get('/settings',         [SettingsController::class, 'index'],      'settings.index');
        $r->post('/settings/update', [SettingsController::class, 'update'],     'settings.update');
        $r->post('/settings/logo',   [SettingsController::class, 'uploadLogo'], 'settings.logo');

        // Vehicle attributes (admin)
        $r->get('/vehicle-attributes',                         [VehicleAttributeController::class, 'index'],       'vattrs.index');
        $r->post('/vehicle-attributes/makes/store',            [VehicleAttributeController::class, 'storeMake'],   'vattrs.makes.store');
        $r->post('/vehicle-attributes/makes/{id}/update',      [VehicleAttributeController::class, 'updateMake'],  'vattrs.makes.update');
        $r->post('/vehicle-attributes/makes/{id}/delete',      [VehicleAttributeController::class, 'deleteMake'],  'vattrs.makes.delete');
        $r->post('/vehicle-attributes/models/store',           [VehicleAttributeController::class, 'storeModel'],  'vattrs.models.store');
        $r->post('/vehicle-attributes/models/{id}/update',     [VehicleAttributeController::class, 'updateModel'], 'vattrs.models.update');
        $r->post('/vehicle-attributes/models/{id}/delete',     [VehicleAttributeController::class, 'deleteModel'], 'vattrs.models.delete');
        $r->post('/vehicle-attributes/types/store',            [VehicleAttributeController::class, 'storeType'],   'vattrs.types.store');
        $r->post('/vehicle-attributes/types/{id}/update',      [VehicleAttributeController::class, 'updateType'],  'vattrs.types.update');
        $r->post('/vehicle-attributes/types/{id}/delete',      [VehicleAttributeController::class, 'deleteType'],  'vattrs.types.delete');
        $r->post('/vehicle-attributes/colours/store',          [VehicleAttributeController::class, 'storeColour'], 'vattrs.colours.store');
        $r->post('/vehicle-attributes/colours/{id}/update',    [VehicleAttributeController::class, 'updateColour'],'vattrs.colours.update');
        $r->post('/vehicle-attributes/colours/{id}/delete',    [VehicleAttributeController::class, 'deleteColour'],'vattrs.colours.delete');
        // API – search must come before wildcard pattern
        $r->get('/api/vehicle-attributes/models',              [VehicleAttributeController::class, 'apiModels'],   'api.vattrs.models');
        $r->post('/api/vehicle-attributes/{type}/create',      [VehicleAttributeController::class, 'apiCreate'],   'api.vattrs.create');

        // Catalog – search must be registered BEFORE /{categoryId}
        $r->get('/catalog/search',                             [CatalogController::class, 'search'],           'catalog.search');
        $r->get('/catalog',                                    [CatalogController::class, 'index'],            'catalog.index');
        $r->post('/catalog/categories/store',                  [CatalogController::class, 'storeCategory'],    'catalog.categories.store');
        $r->post('/catalog/categories/{id}/update',            [CatalogController::class, 'updateCategory'],   'catalog.categories.update');
        $r->post('/catalog/categories/{id}/delete',            [CatalogController::class, 'deleteCategory'],   'catalog.categories.delete');
        $r->get('/catalog/{categoryId}',                       [CatalogController::class, 'showCategory'],     'catalog.category');
        $r->post('/catalog/{categoryId}/items/store',          [CatalogController::class, 'storeItem'],        'catalog.items.store');
        $r->post('/catalog/items/{id}/update',                 [CatalogController::class, 'updateItem'],       'catalog.items.update');
        $r->post('/catalog/items/{id}/delete',                 [CatalogController::class, 'deleteItem'],       'catalog.items.delete');

        // Users (admin)
        $r->get('/users',              [UserController::class, 'index'],  'users.index');
        $r->get('/users/create',       [UserController::class, 'create'], 'users.create');
        $r->post('/users/store',       [UserController::class, 'store'],  'users.store');
        $r->get('/users/{id}/edit',    [UserController::class, 'edit'],   'users.edit');
        $r->post('/users/{id}/update', [UserController::class, 'update'], 'users.update');
        $r->post('/users/{id}/delete', [UserController::class, 'delete'], 'users.delete');

        // Profile
        $r->get('/profile',         [ProfileController::class, 'index'],  'profile.index');
        $r->post('/profile/update', [ProfileController::class, 'update'], 'profile.update');
    }

    public function run(): void
    {
        $this->router->dispatch($this->request, $this->response);
    }
}
