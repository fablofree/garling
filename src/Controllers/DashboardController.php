<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\StatisticsService;
use App\Repositories\CustomerRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\ServiceEntryRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\ExpenseRepository;

class DashboardController extends Controller
{
    public function index(Request $request, Response $response): void
    {
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('/login');
        }

        $statsService = new StatisticsService(
            new CustomerRepository(),
            new VehicleRepository(),
            new ServiceEntryRepository(),
            new PaymentRepository(),
            new ExpenseRepository(),
        );

        $stats = $statsService->getDashboardStats();

        $this->render('dashboard.index', [
            'title'       => 'Dashboard',
            'activeMenu'  => 'dashboard',
            'stats'       => $stats,
        ]);
    }
}
