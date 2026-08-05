<?php
$cfg = $_app ?? [];
$cur = $cfg['currency']['symbol'] ?? 'Rs';
$fmt = fn(float $v) => $cur . ' ' . number_format($v, 2);
$months = ['', 'January','February','March','April','May','June','July','August','September','October','November','December'];
?>
<div class="page-header">
    <h1 class="page-title">Expenses</h1>
    <a href="/expenses/create" class="btn btn-primary">+ Add Expense</a>
</div>

<!-- Month filter -->
<div class="card mb-4">
    <form method="GET" action="/expenses" class="search-form">
        <select name="month" class="form-control">
            <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m ?>" <?= (int)($month ?? date('n')) === $m ? 'selected' : '' ?>><?= $months[$m] ?></option>
            <?php endfor; ?>
        </select>
        <select name="year" class="form-control" style="max-width:110px">
            <?php for ($y = (int)date('Y'); $y >= (int)date('Y') - 5; $y--): ?>
            <option value="<?= $y ?>" <?= (int)($year ?? date('Y')) === $y ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
        <button type="submit" class="btn btn-primary">View</button>
    </form>
</div>

<!-- Category breakdown -->
<?php if (!empty($by_category)): ?>
<div class="card mb-4">
    <div class="card-header"><h2 class="card-title">By Category — <?= $months[(int)($month ?? 1)] ?> <?= (int)($year ?? date('Y')) ?></h2></div>
    <div class="category-pills">
        <?php foreach ($by_category as $cat): ?>
        <div class="category-pill">
            <span class="category-name"><?= htmlspecialchars($cat['category'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
            <span class="category-amount"><?= $fmt((float)($cat['total'] ?? 0)) ?></span>
        </div>
        <?php endforeach; ?>
        <div class="category-pill category-pill-total">
            <span class="category-name">TOTAL</span>
            <span class="category-amount"><?= $fmt((float)($total ?? 0)) ?></span>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Expenses — <?= $months[(int)($month ?? 1)] ?> <?= (int)($year ?? date('Y')) ?></h2>
        <strong><?= $fmt((float)($total ?? 0)) ?></strong>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Reference</th>
                    <th>Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($expenses)): ?>
                <tr><td colspan="6" class="text-center text-muted py-8">No expenses for this period</td></tr>
            <?php else: ?>
                <?php foreach ($expenses as $ex): ?>
                <tr>
                    <td><?= htmlspecialchars($ex['expense_date'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="badge badge-category"><?= htmlspecialchars($ex['category'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td><?= htmlspecialchars($ex['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($ex['reference'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="font-semibold"><?= $fmt((float)($ex['amount'] ?? 0)) ?></td>
                    <td>
                        <div class="action-group">
                            <a href="/expenses/<?= (int)$ex['id'] ?>/edit" class="btn btn-xs btn-outline">Edit</a>
                            <form method="POST" action="/expenses/<?= (int)$ex['id'] ?>/delete" class="inline" onsubmit="return confirm('Delete this expense?')">
                                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
