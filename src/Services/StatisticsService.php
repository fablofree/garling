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
        $outstandingDebt = $this->customerRepo->getTotalOutstandingDebt();
        $totalCustomers  = $this->customerRepo->countAll();
        $totalVehicles   = $this->vehicleRepo->countAll();
        $recentEntries   = $this->serviceRepo->getRecentEntries(8);
        $unpaidInvoices  = $this->serviceRepo->getUnpaidInvoices();
        $debtors         = $this->customerRepo->findWithDebt();

        return [
            'total_customers'   => $totalCustomers,
            'total_vehicles'    => $totalVehicles,
            'outstanding_debt'  => $outstandingDebt,
            'recent_entries'    => $recentEntries,
            'unpaid_invoices'   => $unpaidInvoices,
            'debtors'           => $debtors,
            'current_month'     => (new \DateTimeImmutable())->format('F Y'),
        ];
    }

    public function getReportData(int $year, int $month): array
    {
        $monthlyRevenue  = $this->getMonthlyRevenue($year, $month);
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

    /**
     * Revenue collected (payments received) for a given month — MySQL compatible.
     */
    private function getMonthlyRevenue(int $year, int $month): float
    {
        // Use paymentRepo via DB directly
        $db  = \App\Core\Database::getInstance();
        $row = $db->fetchOne(
            "SELECT COALESCE(SUM(p.amount), 0) AS revenue
             FROM payments p
             JOIN service_entries se ON se.id = p.service_entry_id
             WHERE YEAR(p.payment_date) = :year
               AND MONTH(p.payment_date) = :month
               AND se.is_quotation = 0",
            ['year' => $year, 'month' => $month]
        );
        return (float)($row['revenue'] ?? 0);
    }
}
