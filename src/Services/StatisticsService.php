<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CustomerRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\ServiceEntryRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\ExpenseRepository;

class StatisticsService
{
    public function __construct(
        private readonly CustomerRepository $customerRepo,
        private readonly VehicleRepository $vehicleRepo,
        private readonly ServiceEntryRepository $serviceRepo,
        private readonly PaymentRepository $paymentRepo,
        private readonly ExpenseRepository $expenseRepo,
    ) {}

    public function getDashboardStats(): array
    {
        $now   = new \DateTimeImmutable();
        $year  = (int)$now->format('Y');
        $month = (int)$now->format('n');

        $monthlyRevenue  = $this->serviceRepo->getMonthlyRevenue($year, $month);
        $monthlyExpenses = $this->expenseRepo->getMonthlyTotal($year, $month);
        $outstandingDebt = $this->customerRepo->getTotalOutstandingDebt();
        $totalCustomers  = $this->customerRepo->countAll();
        $totalVehicles   = $this->vehicleRepo->countAll();
        $recentEntries   = $this->serviceRepo->getRecentEntries(8);
        $unpaidInvoices  = $this->serviceRepo->getUnpaidInvoices();
        $debtors         = $this->customerRepo->findWithDebt();
        $dailyRevenue    = $this->paymentRepo->getDailyRevenue($now->format('Y-m-d'));
        $weeklyRevenue   = $this->paymentRepo->getWeeklyRevenue();

        return [
            'total_customers'   => $totalCustomers,
            'total_vehicles'    => $totalVehicles,
            'monthly_revenue'   => $monthlyRevenue,
            'monthly_expenses'  => $monthlyExpenses,
            'monthly_profit'    => $monthlyRevenue - $monthlyExpenses,
            'outstanding_debt'  => $outstandingDebt,
            'daily_revenue'     => $dailyRevenue,
            'weekly_revenue'    => $weeklyRevenue,
            'recent_entries'    => $recentEntries,
            'unpaid_invoices'   => $unpaidInvoices,
            'debtors'           => $debtors,
            'current_month'     => $now->format('F Y'),
        ];
    }

    public function getReportData(int $year, int $month): array
    {
        $monthlyRevenue  = $this->serviceRepo->getMonthlyRevenue($year, $month);
        $monthlyExpenses = $this->expenseRepo->getMonthlyTotal($year, $month);
        $expenseBreakdown = $this->expenseRepo->getTotalByCategory($year, $month);
        $debtors         = $this->customerRepo->findWithDebt();
        $outstandingDebt = $this->customerRepo->getTotalOutstandingDebt();
        $expenseList     = $this->expenseRepo->findByMonth($year, $month);

        return [
            'year'              => $year,
            'month'             => $month,
            'month_name'        => \DateTimeImmutable::createFromFormat('!m', (string)$month)->format('F'),
            'monthly_revenue'   => $monthlyRevenue,
            'monthly_expenses'  => $monthlyExpenses,
            'monthly_profit'    => $monthlyRevenue - $monthlyExpenses,
            'expense_breakdown' => $expenseBreakdown,
            'expense_list'      => $expenseList,
            'outstanding_debt'  => $outstandingDebt,
            'debtors'           => $debtors,
        ];
    }
}
