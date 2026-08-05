<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\ExpenseRepository;

class ExpenseController extends Controller
{
    private ExpenseRepository $expenseRepo;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->expenseRepo = new ExpenseRepository();
    }

    public function index(Request $request, Response $response): void
    {
        $this->requireAuth();

        $year  = (int)$request->get('year', date('Y'));
        $month = (int)$request->get('month', date('n'));

        $expenses = $this->expenseRepo->findByMonth($year, $month);
        $total    = $this->expenseRepo->getMonthlyTotal($year, $month);
        $byCategory = $this->expenseRepo->getTotalByCategory($year, $month);

        $this->render('expenses.index', [
            'title'       => 'Expenses',
            'activeMenu'  => 'expenses',
            'expenses'    => $expenses,
            'total'       => $total,
            'by_category' => $byCategory,
            'year'        => $year,
            'month'       => $month,
        ]);
    }

    public function create(Request $request, Response $response): void
    {
        $this->requireAuth();

        $config = require ROOT_PATH . '/config/app.php';

        $this->render('expenses.form', [
            'title'      => 'New Expense',
            'activeMenu' => 'expenses',
            'expense'    => null,
            'categories' => $config['expense_categories'],
            'action'     => '/expenses/store',
            'submitLabel'=> 'Add Expense',
        ]);
    }

    public function store(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $data = $this->extractExpenseData($request);
        $errors = $this->validateExpenseData($data);

        if (!empty($errors)) {
            Session::flash('error', implode('<br>', $errors));
            $this->redirect('/expenses/create');
        }

        $this->expenseRepo->create($data);
        Session::flash('success', 'Expense recorded successfully.');
        $this->redirect('/expenses');
    }

    public function edit(Request $request, Response $response): void
    {
        $this->requireAuth();

        $id      = (int)$request->param('id');
        $expense = $this->expenseRepo->findById($id);

        if (!$expense) {
            Session::flash('error', 'Expense not found.');
            $this->redirect('/expenses');
        }

        $config = require ROOT_PATH . '/config/app.php';

        $this->render('expenses.form', [
            'title'      => 'Edit Expense',
            'activeMenu' => 'expenses',
            'expense'    => $expense,
            'categories' => $config['expense_categories'],
            'action'     => '/expenses/' . $id . '/update',
            'submitLabel'=> 'Update Expense',
        ]);
    }

    public function update(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $id   = (int)$request->param('id');
        $data = $this->extractExpenseData($request);
        $errors = $this->validateExpenseData($data);

        if (!empty($errors)) {
            Session::flash('error', implode('<br>', $errors));
            $this->redirect('/expenses/' . $id . '/edit');
        }

        $this->expenseRepo->update($id, $data);
        Session::flash('success', 'Expense updated successfully.');
        $this->redirect('/expenses');
    }

    public function delete(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $id = (int)$request->param('id');
        $this->expenseRepo->delete($id);
        Session::flash('success', 'Expense deleted.');
        $this->redirect('/expenses');
    }

    private function extractExpenseData(Request $request): array
    {
        return [
            'expense_date' => $request->post('expense_date', date('Y-m-d')),
            'category'     => trim($request->post('category', '')),
            'description'  => trim($request->post('description', '')),
            'amount'       => (float)$request->post('amount', 0),
            'reference'    => trim($request->post('reference', '')),
            'notes'        => trim($request->post('notes', '')),
        ];
    }

    private function validateExpenseData(array $data): array
    {
        $errors = [];
        if (empty($data['description'])) {
            $errors[] = 'Description is required.';
        }
        if (empty($data['category'])) {
            $errors[] = 'Category is required.';
        }
        if ($data['amount'] <= 0) {
            $errors[] = 'Amount must be greater than zero.';
        }
        return $errors;
    }
}
