<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\CatalogRepository;

class CatalogController extends Controller
{
    private CatalogRepository $catalogRepo;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->catalogRepo = new CatalogRepository();
    }

    /** GET /catalog */
    public function index(Request $request, Response $response): void
    {
        $this->requireAuth();

        $this->render('catalog.index', [
            'title'      => 'Parts Catalog',
            'activeMenu' => 'catalog',
            'categories' => $this->catalogRepo->getCategories(),
        ]);
    }

    /** GET /catalog/{categoryId} */
    public function showCategory(Request $request, Response $response): void
    {
        $this->requireAuth();

        $categoryId = (int)$request->param('categoryId');
        $category   = $this->catalogRepo->getCategoryById($categoryId);

        if (!$category) {
            Session::flash('error', 'Category not found.');
            $this->redirect('/catalog');
        }

        $this->render('catalog.category', [
            'title'      => 'Catalog: ' . htmlspecialchars($category['name']),
            'activeMenu' => 'catalog',
            'category'   => $category,
            'items'      => $this->catalogRepo->getItemsByCategory($categoryId),
            'categories' => $this->catalogRepo->getCategories(),
        ]);
    }

    // ── Categories ─────────────────────────────────────────────────────

    /** POST /catalog/categories/store */
    public function storeCategory(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $name = trim($request->post('name', ''));
        if ($name) {
            try {
                $this->catalogRepo->createCategory($name);
                Session::flash('success', "Category \"{$name}\" created.");
            } catch (\Throwable $e) {
                Session::flash('error', 'Could not create category: ' . $e->getMessage());
            }
        }
        $this->redirect('/catalog');
    }

    /** POST /catalog/categories/{id}/update */
    public function updateCategory(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $id   = (int)$request->param('id');
        $name = trim($request->post('name', ''));
        if ($name) {
            $this->catalogRepo->updateCategory($id, $name);
            Session::flash('success', 'Category updated.');
        }
        $this->redirect('/catalog');
    }

    /** POST /catalog/categories/{id}/delete */
    public function deleteCategory(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $this->catalogRepo->deleteCategory((int)$request->param('id'));
        Session::flash('success', 'Category deleted.');
        $this->redirect('/catalog');
    }

    // ── Items ──────────────────────────────────────────────────────────

    /** POST /catalog/{categoryId}/items/store */
    public function storeItem(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $categoryId = (int)$request->param('categoryId');
        $name       = trim($request->post('name', ''));

        if ($name) {
            try {
                $this->catalogRepo->createItem([
                    'category_id' => $categoryId,
                    'name'        => $name,
                    'description' => trim($request->post('description', '')),
                    'unit_price'  => (float)$request->post('unit_price', 0),
                ]);
                Session::flash('success', "Item \"{$name}\" added.");
            } catch (\Throwable $e) {
                Session::flash('error', 'Could not add item: ' . $e->getMessage());
            }
        }
        $this->redirect('/catalog/' . $categoryId);
    }

    /** POST /catalog/items/{id}/update */
    public function updateItem(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $id         = (int)$request->param('id');
        $item       = $this->catalogRepo->getItemById($id);
        $categoryId = $item ? (int)$item['category_id'] : 0;
        $name       = trim($request->post('name', ''));

        if ($name) {
            $this->catalogRepo->updateItem($id, [
                'category_id' => (int)$request->post('category_id', $categoryId),
                'name'        => $name,
                'description' => trim($request->post('description', '')),
                'unit_price'  => (float)$request->post('unit_price', 0),
            ]);
            Session::flash('success', 'Item updated.');
        }
        $this->redirect('/catalog/' . $categoryId);
    }

    /** POST /catalog/items/{id}/delete */
    public function deleteItem(Request $request, Response $response): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $id   = (int)$request->param('id');
        $item = $this->catalogRepo->getItemById($id);
        $categoryId = $item ? (int)$item['category_id'] : 0;
        $this->catalogRepo->deleteItem($id);
        Session::flash('success', 'Item deleted.');
        $this->redirect('/catalog/' . ($categoryId ?: ''));
    }

    /** GET /catalog/search?q=... — returns JSON */
    public function search(Request $request, Response $response): void
    {
        $this->requireAuth();

        header('Content-Type: application/json');
        $q = trim($request->get('q', ''));
        if (strlen($q) < 1) {
            echo json_encode([]);
            exit;
        }

        $items = $this->catalogRepo->searchItems($q);
        echo json_encode(array_map(fn($i) => [
            'id'            => (int)$i['id'],
            'name'          => $i['name'],
            'unit_price'    => (float)$i['unit_price'],
            'category_name' => $i['category_name'],
        ], $items));
        exit;
    }
}
