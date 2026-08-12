<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\PaymentRepository;
use App\Repositories\ServiceEntryRepository;

class PaymentController extends Controller
{
    private PaymentRepository $paymentRepo;
    private ServiceEntryRepository $serviceRepo;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->paymentRepo = new PaymentRepository();
        $this->serviceRepo = new ServiceEntryRepository();
    }

    public function create(Request $request, Response $response): void
    {
        $this->requireAuth();

        $serviceId = (int)$request->param('serviceId');
        $entry     = $this->serviceRepo->findById($serviceId);

        if (!$entry) {
            Session::flash('error', 'Service entry not found.');
            $this->redirect('/services');
        }

        $totalPaid = $this->paymentRepo->getTotalPaidForEntry($serviceId);
        $balance   = (float)$entry['total_cost'] - $totalPaid;

        $this->render('payments.form', [
            'title'      => 'Record Payment',
            'activeMenu' => 'services',
            'entry'      => $entry,
            'total_paid' => $totalPaid,
            'balance'    => $balance,
            'action'     => '/services/' . $serviceId . '/payments/store',
        ]);
    }

    public function store(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $serviceId = (int)$request->param('serviceId');
        $entry     = $this->serviceRepo->findById($serviceId);

        if (!$entry) {
            Session::flash('error', 'Service entry not found.');
            $this->redirect('/services');
        }

        $amount = (float)$request->post('amount', 0);
        if ($amount <= 0) {
            Session::flash('error', 'Payment amount must be greater than zero.');
            $this->redirect('/services/' . $serviceId . '/payments/create');
        }

        $paymentDate = $request->post('payment_date', date('Y-m-d'));
        if (!empty($paymentDate) && $paymentDate > date('Y-m-d')) {
            Session::flash('error', 'Payment date cannot be in the future.');
            $this->redirect('/services/' . $serviceId . '/payments/create');
        }

        $totalPaid = $this->paymentRepo->getTotalPaidForEntry($serviceId);
        $balance   = (float)$entry['total_cost'] - $totalPaid;

        if ($amount > $balance + 0.01) {
            Session::flash('warning', 'Payment amount exceeds outstanding balance. Proceeding with overpayment.');
        }

        $data = [
            'service_entry_id' => $serviceId,
            'payment_date'     => $paymentDate,
            'amount'           => $amount,
            'payment_method'   => $request->post('payment_method', 'CASH'),
            'cheque_number'    => trim($request->post('cheque_number', '')),
            'reference'        => trim($request->post('reference', '')),
            'notes'            => trim($request->post('notes', '')),
        ];

        $this->paymentRepo->create($data);
        Session::flash('success', 'Payment recorded successfully.');
        $this->redirect('/services/' . $serviceId);
    }
}
