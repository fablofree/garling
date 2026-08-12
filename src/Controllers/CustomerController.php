<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Customer;
use App\Repositories\CustomerRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\ServiceEntryRepository;

class CustomerController extends Controller
{
    private CustomerRepository $customerRepo;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->customerRepo = new CustomerRepository();
    }

    public function index(Request $request, Response $response): void
    {
        $this->requireAuth();

        $search    = trim($request->get('search', ''));
        $customers = $search
            ? $this->customerRepo->search($search)
            : $this->customerRepo->findAll();

        $this->render('customers.index', [
            'title'      => 'Customers',
            'activeMenu' => 'customers',
            'customers'  => $customers,
            'search'     => $search,
        ]);
    }

    public function create(Request $request, Response $response): void
    {
        $this->requireAuth();

        $this->render('customers.form', [
            'title'      => 'New Customer',
            'activeMenu' => 'customers',
            'customer'   => null,
            'action'     => '/customers/store',
            'submitLabel'=> 'Create Customer',
        ]);
    }

    public function store(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $data = $this->extractCustomerData($request);
        $errors = $this->validateCustomerData($data);

        if (!empty($errors)) {
            Session::flash('error', implode('<br>', $errors));
            $this->redirect('/customers/create');
        }

        $id = $this->customerRepo->create($data);
        Session::flash('success', 'Customer created successfully.');
        $this->redirect('/customers/' . $id);
    }

    public function show(Request $request, Response $response): void
    {
        $this->requireAuth();

        $id       = (int)$request->param('id');
        $customer = $this->customerRepo->findById($id);

        if (!$customer) {
            Session::flash('error', 'Customer not found.');
            $this->redirect('/customers');
        }

        $vehicleRepo  = new VehicleRepository();
        $serviceRepo  = new ServiceEntryRepository();
        $vehicles     = $vehicleRepo->findByCustomerId($id);
        $services     = $serviceRepo->findByCustomerId($id);

        $this->render('customers.show', [
            'title'      => 'Customer: ' . htmlspecialchars($customer['name']),
            'activeMenu' => 'customers',
            'customer'   => $customer,
            'vehicles'   => $vehicles,
            'services'   => $services,
        ]);
    }

    public function edit(Request $request, Response $response): void
    {
        $this->requireAuth();

        $id       = (int)$request->param('id');
        $customer = $this->customerRepo->findById($id);

        if (!$customer) {
            Session::flash('error', 'Customer not found.');
            $this->redirect('/customers');
        }

        $this->render('customers.form', [
            'title'       => 'Edit Customer',
            'activeMenu'  => 'customers',
            'customer'    => $customer,
            'action'      => '/customers/' . $id . '/update',
            'submitLabel' => 'Update Customer',
        ]);
    }

    public function update(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $id   = (int)$request->param('id');
        $data = $this->extractCustomerData($request);

        $errors = $this->validateCustomerData($data);
        if (!empty($errors)) {
            Session::flash('error', implode('<br>', $errors));
            $this->redirect('/customers/' . $id . '/edit');
        }

        $this->customerRepo->update($id, $data);
        Session::flash('success', 'Customer updated successfully.');
        $this->redirect('/customers/' . $id);
    }

    public function delete(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $id = (int)$request->param('id');
        $this->customerRepo->delete($id);
        Session::flash('success', 'Customer deleted.');
        $this->redirect('/customers');
    }

    private function extractCustomerData(Request $request): array
    {
        return [
            'name'       => trim($request->post('name', '')),
            'address'    => trim($request->post('address', '')),
            'tel_home'   => trim($request->post('tel_home', '')),
            'tel_office' => trim($request->post('tel_office', '')),
            'tel_mobile' => trim($request->post('tel_mobile', '')),
            'fax'        => trim($request->post('fax', '')),
            'email'      => trim($request->post('email', '')),
            'brn'        => trim($request->post('brn', '')),
            'vat_number' => trim($request->post('vat_number', '')),
            'notes'      => trim($request->post('notes', '')),
        ];
    }

    private function validateCustomerData(array $data): array
    {
        $errors = [];
        if (empty($data['name'])) {
            $errors[] = 'Customer name is required.';
        }
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address.';
        }
        return $errors;
    }
}
