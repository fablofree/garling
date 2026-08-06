<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\VehicleAttributeRepository;

class VehicleAttributeController extends Controller
{
    private VehicleAttributeRepository $repo;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->repo = new VehicleAttributeRepository();
    }

    /** GET /vehicle-attributes */
    public function index(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        $this->render('vehicle_attributes.index', [
            'title'      => 'Vehicle Attributes',
            'activeMenu' => 'vehicle-attributes',
            'makes'      => $this->repo->getMakes(),
            'models'     => $this->repo->getModels(),
            'types'      => $this->repo->getTypes(),
            'colours'    => $this->repo->getColours(),
        ]);
    }

    // ── Makes ──────────────────────────────────────────────────────────

    public function storeMake(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->requireAdmin();
        $this->validateCsrf();
        $name = trim($request->post('name', ''));
        if ($name) {
            try {
                $this->repo->createMake($name);
                Session::flash('success', "Make \"{$name}\" added.");
            } catch (\Throwable $e) {
                Session::flash('error', 'Could not add make: ' . $e->getMessage());
            }
        }
        $this->redirect('/vehicle-attributes#makes');
    }

    public function updateMake(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->requireAdmin();
        $this->validateCsrf();
        $id   = (int)$request->param('id');
        $name = trim($request->post('name', ''));
        if ($name) {
            $this->repo->updateMake($id, $name);
            Session::flash('success', 'Make updated.');
        }
        $this->redirect('/vehicle-attributes#makes');
    }

    public function deleteMake(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->requireAdmin();
        $this->validateCsrf();
        $this->repo->deleteMake((int)$request->param('id'));
        Session::flash('success', 'Make deleted.');
        $this->redirect('/vehicle-attributes#makes');
    }

    // ── Models ─────────────────────────────────────────────────────────

    public function storeModel(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->requireAdmin();
        $this->validateCsrf();
        $name   = trim($request->post('name', ''));
        $makeId = (int)$request->post('make_id', 0) ?: null;
        if ($name) {
            try {
                $this->repo->createModel($name, $makeId);
                Session::flash('success', "Model \"{$name}\" added.");
            } catch (\Throwable $e) {
                Session::flash('error', 'Could not add model: ' . $e->getMessage());
            }
        }
        $this->redirect('/vehicle-attributes#models');
    }

    public function updateModel(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->requireAdmin();
        $this->validateCsrf();
        $id     = (int)$request->param('id');
        $name   = trim($request->post('name', ''));
        $makeId = (int)$request->post('make_id', 0) ?: null;
        if ($name) {
            $this->repo->updateModel($id, $name, $makeId);
            Session::flash('success', 'Model updated.');
        }
        $this->redirect('/vehicle-attributes#models');
    }

    public function deleteModel(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->requireAdmin();
        $this->validateCsrf();
        $this->repo->deleteModel((int)$request->param('id'));
        Session::flash('success', 'Model deleted.');
        $this->redirect('/vehicle-attributes#models');
    }

    // ── Types ──────────────────────────────────────────────────────────

    public function storeType(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->requireAdmin();
        $this->validateCsrf();
        $name = trim($request->post('name', ''));
        if ($name) {
            try {
                $this->repo->createType($name);
                Session::flash('success', "Type \"{$name}\" added.");
            } catch (\Throwable $e) {
                Session::flash('error', 'Could not add type: ' . $e->getMessage());
            }
        }
        $this->redirect('/vehicle-attributes#types');
    }

    public function updateType(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->requireAdmin();
        $this->validateCsrf();
        $id   = (int)$request->param('id');
        $name = trim($request->post('name', ''));
        if ($name) {
            $this->repo->updateType($id, $name);
            Session::flash('success', 'Type updated.');
        }
        $this->redirect('/vehicle-attributes#types');
    }

    public function deleteType(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->requireAdmin();
        $this->validateCsrf();
        $this->repo->deleteType((int)$request->param('id'));
        Session::flash('success', 'Type deleted.');
        $this->redirect('/vehicle-attributes#types');
    }

    // ── Colours ────────────────────────────────────────────────────────

    public function storeColour(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->requireAdmin();
        $this->validateCsrf();
        $name = trim($request->post('name', ''));
        if ($name) {
            try {
                $this->repo->createColour($name);
                Session::flash('success', "Colour \"{$name}\" added.");
            } catch (\Throwable $e) {
                Session::flash('error', 'Could not add colour: ' . $e->getMessage());
            }
        }
        $this->redirect('/vehicle-attributes#colours');
    }

    public function updateColour(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->requireAdmin();
        $this->validateCsrf();
        $id   = (int)$request->param('id');
        $name = trim($request->post('name', ''));
        if ($name) {
            $this->repo->updateColour($id, $name);
            Session::flash('success', 'Colour updated.');
        }
        $this->redirect('/vehicle-attributes#colours');
    }

    public function deleteColour(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->requireAdmin();
        $this->validateCsrf();
        $this->repo->deleteColour((int)$request->param('id'));
        Session::flash('success', 'Colour deleted.');
        $this->redirect('/vehicle-attributes#colours');
    }

    // ── JSON API ───────────────────────────────────────────────────────

    /** POST /api/vehicle-attributes/{type}/create */
    public function apiCreate(Request $request, Response $response): void
    {
        $this->requireAuth();

        header('Content-Type: application/json');

        // Validate CSRF token from POST body
        $token = $request->post('_csrf_token', '');
        if (!Session::validateCsrfToken($token)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Invalid security token.']);
            exit;
        }

        $type = $request->param('type');
        $name = trim($request->post('name', ''));

        if (!$name) {
            http_response_code(422);
            echo json_encode(['success' => false, 'error' => 'Name is required.']);
            exit;
        }

        try {
            $row = match ($type) {
                'makes'   => $this->repo->createMake($name),
                'models'  => $this->repo->createModel(
                    $name,
                    (int)$request->post('make_id', 0) ?: null
                ),
                'types'   => $this->repo->createType($name),
                'colours' => $this->repo->createColour($name),
                default   => throw new \InvalidArgumentException('Invalid attribute type.'),
            };

            echo json_encode([
                'success' => true,
                'id'      => (int)($row['id'] ?? 0),
                'name'    => $row['name'] ?? $name,
            ]);
        } catch (\Throwable $e) {
            http_response_code(409);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    /** GET /api/vehicle-attributes/models?make_id=N */
    public function apiModels(Request $request, Response $response): void
    {
        $this->requireAuth();

        header('Content-Type: application/json');
        $makeId = (int)$request->get('make_id', 0) ?: null;
        $models = $this->repo->getModels($makeId);

        echo json_encode(array_map(fn($m) => [
            'id'      => (int)$m['id'],
            'name'    => $m['name'],
            'make_id' => $m['make_id'],
        ], $models));
        exit;
    }

    private function requireAdmin(): void
    {
        if (Session::get('user_role') !== 'admin') {
            Session::flash('error', 'Access denied. Admin only.');
            $this->redirect('/dashboard');
        }
    }
}
