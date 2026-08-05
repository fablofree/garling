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

class ReportController extends Controller
{
    public function index(Request $request, Response $response): void
    {
        $this->requireAuth();

        $year  = (int)$request->get('year', date('Y'));
        $month = (int)$request->get('month', date('n'));

        $statsService = new StatisticsService(
            new CustomerRepository(),
            new VehicleRepository(),
            new ServiceEntryRepository(),
            new PaymentRepository(),
            new ExpenseRepository(),
        );

        $data = $statsService->getReportData($year, $month);

        $this->render('reports.index', array_merge($data, [
            'title'      => 'Reports',
            'activeMenu' => 'reports',
        ]));
    }
}
