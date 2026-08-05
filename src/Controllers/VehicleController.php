<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\VehicleRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\ServiceEntryRepository;

class VehicleController extends Controller
{
    private VehicleRepository $vehicleRepo;
    private CustomerRepository $customerRepo;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->vehicleRepo  = new VehicleRepository();
        $this->customerRepo = new CustomerRepository();
    }

    public function index(Request $request, Response $response): void
    {
        $this->requireAuth();

        $search   = trim($request->get('search', ''));
        $vehicles = $search
            ? $this->vehicleRepo->search($search)
            : $this->vehicleRepo->findAll();

        $this->render('vehicles.index', [
            'title'      => 'Vehicles',
            'activeMenu' => 'vehicles',
            'vehicles'   => $vehicles,
            'search'     => $search,
        ]);
    }

    public function create(Request $request, Response $response): void
    {
        $this->requireAuth();

        $customerId = (int)$request->get('customer_id', 0);
        $customers  = $this->customerRepo->findAll();

        $this->render('vehicles.form', [
            'title'         => 'New Vehicle',
            'activeMenu'    => 'vehicles',
            'vehicle'       => null,
            'customers'     => $customers,
            'action'        => '/vehicles/store',
            'submitLabel'   => 'Register Vehicle',
            'selectedCustomer' => $customerId,
        ]);
    }

    public function store(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $data   = $this->extractVehicleData($request);
        $errors = $this->validateVehicleData($data);

        if (!empty($errors)) {
            Session::flash('error', implode('<br>', $errors));
            $this->redirect('/vehicles/create');
        }

        $id = $this->vehicleRepo->create($data);
        Session::flash('success', 'Vehicle registered successfully.');
        $this->redirect('/vehicles/' . $id);
    }

    public function show(Request $request, Response $response): void
    {
        $this->requireAuth();

        $id      = (int)$request->param('id');
        $vehicle = $this->vehicleRepo->findById($id);

        if (!$vehicle) {
            Session::flash('error', 'Vehicle not found.');
            $this->redirect('/vehicles');
        }

        $serviceRepo = new ServiceEntryRepository();
        $services    = $serviceRepo->findByVehicleId($id);

        $this->render('vehicles.show', [
            'title'      => 'Vehicle: ' . htmlspecialchars($vehicle['registration_no']),
            'activeMenu' => 'vehicles',
            'vehicle'    => $vehicle,
            'services'   => $services,
        ]);
    }

    public function edit(Request $request, Response $response): void
    {
        $this->requireAuth();

        $id      = (int)$request->param('id');
        $vehicle = $this->vehicleRepo->findById($id);

        if (!$vehicle) {
            Session::flash('error', 'Vehicle not found.');
            $this->redirect('/vehicles');
        }

        $customers = $this->customerRepo->findAll();

        $this->render('vehicles.form', [
            'title'      => 'Edit Vehicle',
            'activeMenu' => 'vehicles',
            'vehicle'    => $vehicle,
            'customers'  => $customers,
            'action'     => '/vehicles/' . $id . '/update',
            'submitLabel'=> 'Update Vehicle',
            'selectedCustomer' => $vehicle['customer_id'],
        ]);
    }

    public function update(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $id     = (int)$request->param('id');
        $data   = $this->extractVehicleData($request);
        $errors = $this->validateVehicleData($data);

        if (!empty($errors)) {
            Session::flash('error', implode('<br>', $errors));
            $this->redirect('/vehicles/' . $id . '/edit');
        }

        $this->vehicleRepo->update($id, $data);
        Session::flash('success', 'Vehicle updated successfully.');
        $this->redirect('/vehicles/' . $id);
    }

    public function delete(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $id = (int)$request->param('id');
        $this->vehicleRepo->delete($id);
        Session::flash('success', 'Vehicle deleted.');
        $this->redirect('/vehicles');
    }

    private function extractVehicleData(Request $request): array
    {
        return [
            'customer_id'         => (int)$request->post('customer_id', 0),
            'registration_no'     => strtoupper(trim($request->post('registration_no', ''))),
            'chassis_no'          => trim($request->post('chassis_no', '')),
            'make'                => trim($request->post('make', '')),
            'model'               => trim($request->post('model', '')),
            'vehicle_type'        => trim($request->post('vehicle_type', '')),
            'colour'              => trim($request->post('colour', '')),
            'year'                => $request->post('year') ? (int)$request->post('year') : null,
            'distance_unit'       => $request->post('distance_unit', 'km'),
            'servicing_frequency' => (int)$request->post('servicing_frequency', 5000),
            'notes'               => trim($request->post('notes', '')),
        ];
    }

    private function validateVehicleData(array $data): array
    {
        $errors = [];
        if (empty($data['registration_no'])) {
            $errors[] = 'Registration number is required.';
        }
        if (empty($data['customer_id'])) {
            $errors[] = 'Customer is required.';
        }
        return $errors;
    }
}
