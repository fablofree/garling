<?php
$cfg = $_app ?? [];
$cur = $cfg['currency']['symbol'] ?? 'Rs';
$fmt = fn(float $v) => $cur . ' ' . number_format($v, 2);
?>
<div class="page-header">
    <h1 class="page-title">Invoices</h1>
</div>

<!-- Search -->
<form method="GET" action="/invoices" class="mb-4" style="display:flex;gap:8px;max-width:500px">
    <input type="text" name="search" class="form-control"
           placeholder="Search by invoice no., customer, or vehicle reg..."
           value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <button type="submit" class="btn btn-primary">Search</button>
    <?php if (!empty($search)): ?>
    <a href="/invoices" class="btn btn-outline">Clear</a>
    <?php endif; ?>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice No.</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Vehicle Reg.</th>
                    <th>Total</th>
                    <th>Balance</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($invoices)): ?>
                <tr><td colspan="7" class="text-center text-muted">No invoices found</td></tr>
            <?php else: ?>
                <?php foreach ($invoices as $inv): ?>
                <?php
                    $balance = (float)($inv['balance'] ?? 0);
                    $status  = $balance <= 0 ? 'paid' : ($inv['total_paid'] > 0 ? 'partial' : 'unpaid');
                ?>
                <tr>
                    <td><a href="/services/<?= (int)$inv['service_entry_id'] ?>/invoice" class="link" target="_blank"><?= htmlspecialchars($inv['invoice_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></a></td>
                    <td><?= htmlspecialchars($inv['entry_date'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><a href="/customers/<?= (int)($inv['customer_id'] ?? 0) ?>" class="link"><?= htmlspecialchars($inv['customer_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></a></td>
                    <td><?= htmlspecialchars($inv['registration_no'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= $fmt((float)($inv['total_cost'] ?? 0)) ?></td>
                    <td>
                        <span class="badge badge-<?= $status ?>"><?= $balance <= 0 ? 'Paid' : $fmt($balance) ?></span>
                    </td>
                    <td>
                        <a href="/services/<?= (int)$inv['service_entry_id'] ?>/invoice" class="btn btn-xs btn-outline" target="_blank">View</a>
                        <a href="/services/<?= (int)$inv['service_entry_id'] ?>" class="btn btn-xs btn-outline">Entry</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
