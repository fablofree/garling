<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\InvoiceRepository;

class InvoicesController extends Controller
{
    private InvoiceRepository $invoiceRepo;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->invoiceRepo = new InvoiceRepository();
    }

    public function index(Request $request, Response $response): void
    {
        $this->requireAuth();
        $search   = trim($request->get('search', ''));
        $invoices = $search ? $this->invoiceRepo->search($search) : $this->invoiceRepo->findAll();
        $this->render('invoices.index', [
            'title'      => 'Invoices',
            'activeMenu' => 'invoices',
            'invoices'   => $invoices,
            'search'     => $search,
        ]);
    }
}
