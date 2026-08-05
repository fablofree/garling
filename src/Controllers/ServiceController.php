<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Repositories\ServiceEntryRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\PaymentRepository;
use App\Services\InvoiceService;

class ServiceController extends Controller
{
    private ServiceEntryRepository $serviceRepo;
    private CustomerRepository $customerRepo;
    private VehicleRepository $vehicleRepo;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->serviceRepo  = new ServiceEntryRepository();
        $this->customerRepo = new CustomerRepository();
        $this->vehicleRepo  = new VehicleRepository();
    }

    public function index(Request $request, Response $response): void
    {
        $this->requireAuth();

        $search   = trim($request->get('search', ''));
        $type     = $request->get('type', '');
        $status   = $request->get('status', '');

        $entries = $this->serviceRepo->findAll();

        // Client-side filter (simple in-memory for now)
        if ($type) {
            $entries = array_filter($entries, fn($e) => strtoupper($e['entry_type']) === strtoupper($type));
        }

        $this->render('services.index', [
            'title'      => 'Service Entries',
            'activeMenu' => 'services',
            'entries'    => array_values($entries),
            'search'     => $search,
            'filter_type'=> $type,
        ]);
    }

    public function create(Request $request, Response $response): void
    {
        $this->requireAuth();

        $vehicleId  = (int)$request->get('vehicle_id', 0);
        $customerId = (int)$request->get('customer_id', 0);

        $customers = $this->customerRepo->findAll();
        $vehicles  = $this->vehicleRepo->findAll();

        $config = require ROOT_PATH . '/config/app.php';

        $this->render('services.form', [
            'title'       => 'New Service Entry',
            'activeMenu'  => 'services',
            'entry'       => null,
            'spare_parts' => [],
            'repairs'     => [],
            'customers'   => $customers,
            'vehicles'    => $vehicles,
            'action'      => '/services/store',
            'submitLabel' => 'Create Entry',
            'default_vat' => $config['vat_rate'],
            'preselect_vehicle'  => $vehicleId,
            'preselect_customer' => $customerId,
        ]);
    }

    public function store(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            $entryData = $this->extractEntryData($request);
            $errors = $this->validateEntryData($entryData);

            if (!empty($errors)) {
                $db->rollBack();
                Session::flash('error', implode('<br>', $errors));
                $this->redirect('/services/create');
            }

            // Parse line items
            $spareParts = $this->parseLineItems($request, 'parts');
            $repairs    = $this->parseLineItems($request, 'repairs');

            // Calculate totals
            $invoiceService = new InvoiceService(
                $this->serviceRepo,
                new PaymentRepository(),
                $this->customerRepo,
                $this->vehicleRepo,
            );
            $totals = $invoiceService->calculateTotals(
                $spareParts,
                $repairs,
                (float)$entryData['vat_percent'],
                (float)$entryData['discount_amount']
            );

            $entryData = array_merge($entryData, $totals);
            $id = $this->serviceRepo->create($entryData);

            // Insert line items
            foreach ($spareParts as $i => $part) {
                $this->serviceRepo->insertSparePart($id, $part, $i);
            }
            foreach ($repairs as $i => $repair) {
                $this->serviceRepo->insertRepair($id, $repair, $i);
            }

            $db->commit();
            Session::flash('success', 'Service entry created successfully.');
            $this->redirect('/services/' . $id);
        } catch (\Throwable $e) {
            $db->rollBack();
            Session::flash('error', 'Error: ' . $e->getMessage());
            $this->redirect('/services/create');
        }
    }

    public function show(Request $request, Response $response): void
    {
        $this->requireAuth();

        $id    = (int)$request->param('id');
        $entry = $this->serviceRepo->findById($id);

        if (!$entry) {
            Session::flash('error', 'Service entry not found.');
            $this->redirect('/services');
        }

        $spareParts = $this->serviceRepo->getSpareParts($id);
        $repairs    = $this->serviceRepo->getRepairs($id);
        $paymentRepo = new PaymentRepository();
        $payments   = $paymentRepo->findByServiceEntryId($id);

        $this->render('services.show', [
            'title'       => 'Service Entry #' . str_pad((string)$id, 6, '0', STR_PAD_LEFT),
            'activeMenu'  => 'services',
            'entry'       => $entry,
            'spare_parts' => $spareParts,
            'repairs'     => $repairs,
            'payments'    => $payments,
        ]);
    }

    public function edit(Request $request, Response $response): void
    {
        $this->requireAuth();

        $id    = (int)$request->param('id');
        $entry = $this->serviceRepo->findById($id);

        if (!$entry) {
            Session::flash('error', 'Service entry not found.');
            $this->redirect('/services');
        }

        $spareParts = $this->serviceRepo->getSpareParts($id);
        $repairs    = $this->serviceRepo->getRepairs($id);
        $customers  = $this->customerRepo->findAll();
        $vehicles   = $this->vehicleRepo->findAll();

        $this->render('services.form', [
            'title'       => 'Edit Service Entry',
            'activeMenu'  => 'services',
            'entry'       => $entry,
            'spare_parts' => $spareParts,
            'repairs'     => $repairs,
            'customers'   => $customers,
            'vehicles'    => $vehicles,
            'action'      => '/services/' . $id . '/update',
            'submitLabel' => 'Update Entry',
            'default_vat' => $entry['vat_percent'],
            'preselect_vehicle'  => $entry['vehicle_id'],
            'preselect_customer' => $entry['customer_id'],
        ]);
    }

    public function update(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $id = (int)$request->param('id');
        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            $entryData = $this->extractEntryData($request);
            $errors = $this->validateEntryData($entryData);

            if (!empty($errors)) {
                $db->rollBack();
                Session::flash('error', implode('<br>', $errors));
                $this->redirect('/services/' . $id . '/edit');
            }

            $spareParts = $this->parseLineItems($request, 'parts');
            $repairs    = $this->parseLineItems($request, 'repairs');

            $invoiceService = new InvoiceService(
                $this->serviceRepo,
                new PaymentRepository(),
                $this->customerRepo,
                $this->vehicleRepo,
            );
            $totals = $invoiceService->calculateTotals(
                $spareParts,
                $repairs,
                (float)$entryData['vat_percent'],
                (float)$entryData['discount_amount']
            );

            $entryData = array_merge($entryData, $totals);
            $this->serviceRepo->update($id, $entryData);

            // Replace line items
            $this->serviceRepo->deleteSpareParts($id);
            $this->serviceRepo->deleteRepairs($id);

            foreach ($spareParts as $i => $part) {
                $this->serviceRepo->insertSparePart($id, $part, $i);
            }
            foreach ($repairs as $i => $repair) {
                $this->serviceRepo->insertRepair($id, $repair, $i);
            }

            $db->commit();
            Session::flash('success', 'Service entry updated successfully.');
            $this->redirect('/services/' . $id);
        } catch (\Throwable $e) {
            $db->rollBack();
            Session::flash('error', 'Error: ' . $e->getMessage());
            $this->redirect('/services/' . $id . '/edit');
        }
    }

    public function delete(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $id = (int)$request->param('id');
        $this->serviceRepo->delete($id);
        Session::flash('success', 'Service entry deleted.');
        $this->redirect('/services');
    }

    public function invoice(Request $request, Response $response): void
    {
        $this->requireAuth();

        $id = (int)$request->param('id');

        $invoiceService = new InvoiceService(
            $this->serviceRepo,
            new PaymentRepository(),
            $this->customerRepo,
            $this->vehicleRepo,
        );

        $data = $invoiceService->buildInvoiceData($id);

        if (!$data) {
            Session::flash('error', 'Service entry not found.');
            $this->redirect('/services');
        }

        $this->renderRaw('services.invoice', array_merge($data, [
            'title'      => 'Invoice #' . $data['invoice_number'],
            'activeMenu' => 'services',
        ]));
    }

    private function extractEntryData(Request $request): array
    {
        $isQuotation = (bool)$request->post('is_quotation', false);
        $isCompleted = (bool)$request->post('is_completed', false);
        $entryType   = $isQuotation ? 'QUOTATION' : 'INVOICE';

        return [
            'entry_date'      => $request->post('entry_date', date('Y-m-d')),
            'vehicle_id'      => (int)$request->post('vehicle_id', 0),
            'customer_id'     => (int)$request->post('customer_id', 0),
            'odometer'        => $request->post('odometer') !== '' ? (int)$request->post('odometer') : null,
            'next_servicing'  => $request->post('next_servicing') !== '' ? (int)$request->post('next_servicing') : null,
            'remarks'         => trim($request->post('remarks', '')),
            'entry_type'      => $entryType,
            'is_quotation'    => $isQuotation ? 't' : 'f',
            'is_completed'    => $isCompleted ? 't' : 'f',
            'delivery_date'   => $request->post('delivery_date') ?: null,
            'vat_percent'     => (float)$request->post('vat_percent', 0),
            'discount_amount' => (float)$request->post('discount_amount', 0),
        ];
    }

    private function validateEntryData(array $data): array
    {
        $errors = [];
        if (empty($data['vehicle_id'])) {
            $errors[] = 'Vehicle is required.';
        }
        if (empty($data['customer_id'])) {
            $errors[] = 'Customer is required.';
        }
        if (empty($data['entry_date'])) {
            $errors[] = 'Entry date is required.';
        }
        return $errors;
    }

    private function parseLineItems(Request $request, string $prefix): array
    {
        $descriptions = $request->post($prefix . '_description', []);
        $quantities   = $request->post($prefix . '_quantity', []);
        $unitPrices   = $request->post($prefix . '_unit_price', []);

        if (!is_array($descriptions)) {
            return [];
        }

        $items = [];
        foreach ($descriptions as $i => $desc) {
            $desc = trim($desc);
            if ($desc === '') {
                continue;
            }
            $qty   = (float)($quantities[$i] ?? 1);
            $price = (float)($unitPrices[$i] ?? 0);
            $items[] = [
                'description' => $desc,
                'quantity'    => $qty,
                'unit_price'  => $price,
                'total_price' => round($qty * $price, 2),
            ];
        }

        return $items;
    }
}
