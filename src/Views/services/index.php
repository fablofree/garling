<?php
$cfg = $_app ?? [];
$cur = $cfg['currency']['symbol'] ?? 'Rs';
$fmt = fn(float $v) => $cur . ' ' . number_format($v, 2);
?>
<div class="page-header">
    <h1 class="page-title">Service Entries</h1>
    <a href="/services/create" class="btn btn-primary">+ New Entry</a>
</div>

<div class="card mb-4">
    <form method="GET" action="/services" class="search-form">
        <input type="text" name="search" value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>"
               placeholder="Search..." class="form-control search-input">
        <select name="type" class="form-control" style="max-width:160px">
            <option value="">All Types</option>
            <option value="INVOICE" <?= ($filter_type ?? '') === 'INVOICE' ? 'selected' : '' ?>>Invoice</option>
            <option value="QUOTATION" <?= ($filter_type ?? '') === 'QUOTATION' ? 'selected' : '' ?>>Quotation</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="/services" class="btn btn-outline">Reset</a>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Ref #</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Vehicle</th>
                    <th>Type</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($entries)): ?>
                <tr><td colspan="10" class="text-center text-muted py-8">No service entries found</td></tr>
            <?php else: ?>
                <?php foreach ($entries as $e): ?>
                <?php
                    $total  = (float)($e['total_cost'] ?? 0);
                    $paid   = (float)($e['total_paid'] ?? 0);
                    $bal    = $total - $paid;
                    $status = $bal <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
                ?>
                <tr>
                    <td><a href="/services/<?= (int)$e['id'] ?>" class="link font-semibold"><?= str_pad((string)$e['id'], 6, '0', STR_PAD_LEFT) ?></a></td>
                    <td><?= htmlspecialchars($e['entry_date'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><a href="/customers/<?= (int)($e['customer_id'] ?? 0) ?>" class="link"><?= htmlspecialchars($e['customer_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></a></td>
                    <td><?= htmlspecialchars($e['registration_no'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="badge badge-<?= strtolower($e['entry_type'] ?? 'invoice') ?>"><?= htmlspecialchars($e['entry_type'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td><?= $fmt($total) ?></td>
                    <td class="text-green"><?= $fmt($paid) ?></td>
                    <td class="<?= $bal > 0 ? 'text-red font-semibold' : '' ?>"><?= $fmt($bal) ?></td>
                    <td><span class="badge badge-<?= $status ?>"><?= ucfirst($status) ?></span></td>
                    <td>
                        <div class="action-group">
                            <a href="/services/<?= (int)$e['id'] ?>" class="btn btn-xs btn-outline">View</a>
                            <a href="/services/<?= (int)$e['id'] ?>/invoice" class="btn btn-xs btn-outline" target="_blank">Invoice</a>
                            <a href="/services/<?= (int)$e['id'] ?>/edit" class="btn btn-xs btn-outline">Edit</a>
                            <form method="POST" action="/services/<?= (int)$e['id'] ?>/delete" class="inline" onsubmit="return confirm('Delete this entry?')">
                                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="btn btn-xs btn-danger">Del</button>
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
