<?php
$cfg    = $_app ?? [];
$cur    = $cfg['currency']['symbol'] ?? 'Rs';
$fmt    = fn(float $v) => $cur . ' ' . number_format($v, 2);
$months = ['', 'January','February','March','April','May','June','July','August','September','October','November','December'];
$selMonth = (int)($month ?? date('n'));
$selYear  = (int)($year ?? date('Y'));
?>
<div class="page-header">
    <h1 class="page-title">Reports</h1>
</div>

<!-- Period filter -->
<div class="card mb-6">
    <form method="GET" action="/reports" class="search-form">
        <label class="form-label" style="margin-bottom:0;margin-right:8px">Period:</label>
        <select name="month" class="form-control">
            <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m ?>" <?= $selMonth === $m ? 'selected' : '' ?>><?= $months[$m] ?></option>
            <?php endfor; ?>
        </select>
        <select name="year" class="form-control" style="max-width:110px">
            <?php for ($y = (int)date('Y'); $y >= (int)date('Y') - 5; $y--): ?>
            <option value="<?= $y ?>" <?= $selYear === $y ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
        <button type="submit" class="btn btn-primary">View Report</button>
    </form>
</div>

<h2 class="section-title">Report for <?= htmlspecialchars($months[$selMonth] . ' ' . $selYear, ENT_QUOTES, 'UTF-8') ?></h2>

<!-- Key figures -->
<div class="stats-grid mb-6">
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
        </div>
        <div class="stat-body">
            <div class="stat-value"><?= $fmt((float)($monthly_revenue ?? 0)) ?></div>
            <div class="stat-label">Revenue Collected</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-red">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
        </div>
        <div class="stat-body">
            <div class="stat-value"><?= $fmt((float)($monthly_expenses ?? 0)) ?></div>
            <div class="stat-label">Total Expenses</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-<?= ((float)($monthly_profit ?? 0)) >= 0 ? 'green' : 'red' ?>">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        </div>
        <div class="stat-body">
            <div class="stat-value <?= ((float)($monthly_profit ?? 0)) < 0 ? 'text-red' : 'text-green' ?>"><?= $fmt((float)($monthly_profit ?? 0)) ?></div>
            <div class="stat-label">Net Profit / Loss</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-orange">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        </div>
        <div class="stat-body">
            <div class="stat-value text-red"><?= $fmt((float)($outstanding_debt ?? 0)) ?></div>
            <div class="stat-label">Outstanding Debt (All Time)</div>
        </div>
    </div>
</div>

<div class="dashboard-grid">

    <!-- Expense breakdown -->
    <div class="card">
        <div class="card-header"><h2 class="card-title">Expenses by Category</h2></div>
        <?php if (empty($expense_breakdown)): ?>
            <div class="text-center text-muted py-6">No expenses this period</div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Category</th><th class="text-right">Amount</th></tr></thead>
                <tbody>
                <?php foreach ($expense_breakdown as $cat): ?>
                <tr>
                    <td><?= htmlspecialchars($cat['category'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-right"><?= $fmt((float)($cat['total'] ?? 0)) ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="font-semibold">
                    <td>Total</td>
                    <td class="text-right"><?= $fmt((float)($monthly_expenses ?? 0)) ?></td>
                </tr>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Debtors list -->
    <div class="card">
        <div class="card-header"><h2 class="card-title">Outstanding Customer Balances</h2></div>
        <?php if (empty($debtors)): ?>
            <div class="text-center text-muted py-6">No outstanding balances — great!</div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Customer</th><th>Invoiced</th><th>Paid</th><th class="text-right">Balance</th></tr></thead>
                <tbody>
                <?php foreach ($debtors as $d): ?>
                <tr>
                    <td><a href="/customers/<?= (int)$d['id'] ?>" class="link"><?= htmlspecialchars($d['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></a></td>
                    <td><?= $fmt((float)($d['total_invoiced'] ?? 0)) ?></td>
                    <td><?= $fmt((float)($d['total_paid'] ?? 0)) ?></td>
                    <td class="text-right text-red font-semibold"><?= $fmt((float)($d['balance'] ?? 0)) ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="font-semibold">
                    <td colspan="3">Total Outstanding</td>
                    <td class="text-right text-red"><?= $fmt((float)($outstanding_debt ?? 0)) ?></td>
                </tr>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

</div>

<!-- Expense detail -->
<?php if (!empty($expense_list)): ?>
<div class="card mt-4">
    <div class="card-header"><h2 class="card-title">Expense Detail — <?= $months[$selMonth] ?> <?= $selYear ?></h2></div>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Date</th><th>Category</th><th>Description</th><th class="text-right">Amount</th></tr></thead>
            <tbody>
            <?php foreach ($expense_list as $ex): ?>
            <tr>
                <td><?= htmlspecialchars($ex['expense_date'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($ex['category'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($ex['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td class="text-right"><?= $fmt((float)($ex['amount'] ?? 0)) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
