<?php
$e      = $entry ?? [];
$cfg    = $_app ?? [];
$cur    = $cfg['currency']['symbol'] ?? 'Rs';
$fmt    = fn(float $v) => $cur . ' ' . number_format($v, 2);
$total  = (float)($e['total_cost'] ?? 0);
$paid   = (float)($e['total_paid'] ?? 0);
$bal    = $total - $paid;
$status = $bal <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
$invoiceNo = 'INV-' . str_pad((string)($e['id'] ?? ''), 6, '0', STR_PAD_LEFT);
?>
<div class="page-header">
    <h1 class="page-title"><?= htmlspecialchars($invoiceNo, ENT_QUOTES, 'UTF-8') ?></h1>
    <div class="action-group">
        <a href="/services/<?= (int)($e['id'] ?? 0) ?>/invoice" class="btn btn-outline" target="_blank">Print <?= $e['is_quotation'] == 1 ? 'Quotation' : 'Invoice' ?></a>
        <?php if ($bal > 0 && $e['is_quotation'] != 1): ?>
        <a href="/services/<?= (int)($e['id'] ?? 0) ?>/payments/create" class="btn btn-primary">Record Payment</a>
        <?php endif; ?>
        <a href="/services/<?= (int)($e['id'] ?? 0) ?>/edit" class="btn btn-outline">Edit</a>
        <form method="POST" action="/services/<?= (int)($e['id'] ?? 0) ?>/delete" class="inline" onsubmit="return confirm('Delete this entry?')">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>

<!-- Summary row -->
<div class="stats-grid stats-grid-4 mb-6">
    <div class="stat-card stat-card-sm">
        <div class="stat-body">
            <div class="stat-value"><?= $fmt($total) ?></div>
            <div class="stat-label">Total</div>
        </div>
    </div>
    <div class="stat-card stat-card-sm">
        <div class="stat-body">
            <div class="stat-value text-green"><?= $fmt($paid) ?></div>
            <div class="stat-label">Paid</div>
        </div>
    </div>
    <div class="stat-card stat-card-sm">
        <div class="stat-body">
            <div class="stat-value <?= $bal > 0 ? 'text-red' : 'text-green' ?>"><?= $fmt($bal) ?></div>
            <div class="stat-label">Balance</div>
        </div>
    </div>
    <div class="stat-card stat-card-sm">
        <div class="stat-body">
            <span class="badge badge-<?= $status ?>" style="font-size:1rem"><?= ucfirst($status) ?></span>
            <div class="stat-label">Status</div>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Entry details -->
    <div class="card">
        <div class="card-header"><h2 class="card-title">Entry Details</h2></div>
        <div class="detail-list">
            <div class="detail-row"><span class="detail-label">Date</span><span><?= htmlspecialchars($e['entry_date'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="detail-row"><span class="detail-label">Customer</span><span><a href="/customers/<?= (int)($e['customer_id'] ?? 0) ?>" class="link"><?= htmlspecialchars($e['customer_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></a></span></div>
            <div class="detail-row"><span class="detail-label">Vehicle</span><span><a href="/vehicles/<?= (int)($e['vehicle_id'] ?? 0) ?>" class="link"><?= htmlspecialchars($e['registration_no'] ?? '', ENT_QUOTES, 'UTF-8') ?></a> <?= htmlspecialchars(trim(($e['vehicle_make'] ?? '') . ' ' . ($e['vehicle_model'] ?? '')), ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="detail-row"><span class="detail-label">Odometer</span><span><?= $e['odometer'] ? number_format((int)$e['odometer']) . ' ' . ($e['distance_unit'] ?? 'km') : '—' ?></span></div>
            <?php if ($e['is_quotation'] != 1): ?>
            <div class="detail-row"><span class="detail-label">Next Service</span><span><?= $e['next_servicing'] ? number_format((int)$e['next_servicing']) . ' ' . ($e['distance_unit'] ?? 'km') : '—' ?></span></div>
            <?php endif; ?>
            <div class="detail-row"><span class="detail-label">Type</span><span><span class="badge badge-<?= strtolower($e['entry_type'] ?? '') ?>"><?= htmlspecialchars($e['entry_type'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></span></div>
            <div class="detail-row"><span class="detail-label">Delivery Date</span><span><?= htmlspecialchars($e['delivery_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <?php if (!empty($e['remarks'])): ?>
            <div class="detail-row"><span class="detail-label">Remarks</span><span><?= nl2br(htmlspecialchars($e['remarks'], ENT_QUOTES, 'UTF-8')) ?></span></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Financial summary -->
    <div class="card">
        <div class="card-header"><h2 class="card-title">Financial Summary</h2></div>
        <div class="detail-list">
            <div class="detail-row"><span class="detail-label">Parts</span><span><?= $fmt((float)($e['total_parts'] ?? 0)) ?></span></div>
            <div class="detail-row"><span class="detail-label">Labour</span><span><?= $fmt((float)($e['total_labour'] ?? 0)) ?></span></div>
            <div class="detail-row"><span class="detail-label">Discount</span><span><?= $fmt((float)($e['discount_amount'] ?? 0)) ?></span></div>
            <div class="detail-row"><span class="detail-label">Subtotal</span><span><?= $fmt((float)($e['subtotal'] ?? 0)) ?></span></div>
            <div class="detail-row"><span class="detail-label">VAT (<?= (float)($e['vat_percent'] ?? 0) ?>%)</span><span><?= $fmt((float)($e['vat_amount'] ?? 0)) ?></span></div>
            <div class="detail-row font-semibold"><span class="detail-label">TOTAL</span><span><?= $fmt($total) ?></span></div>
        </div>
    </div>
</div>

<!-- Spare parts -->
<?php if (!empty($spare_parts)): ?>
<div class="card mt-4">
    <div class="card-header"><h2 class="card-title">Spare Parts</h2></div>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Description</th><th>Amount</th></tr></thead>
            <tbody>
            <?php foreach ($spare_parts as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= $fmt((float)($p['amount'] ?? 0)) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Repairs -->
<?php if (!empty($repairs)): ?>
<div class="card mt-4">
    <div class="card-header"><h2 class="card-title">Repairs / Labour</h2></div>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Description</th><th>Amount</th></tr></thead>
            <tbody>
            <?php foreach ($repairs as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= $fmt((float)($r['amount'] ?? 0)) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Payments -->
<div class="card mt-4">
    <div class="card-header">
        <h2 class="card-title">Payments (<?= count($payments ?? []) ?>)</h2>
        <?php if ($bal > 0 && $e['is_quotation'] != 1): ?>
        <a href="/services/<?= (int)($e['id'] ?? 0) ?>/payments/create" class="btn btn-sm btn-primary">+ Record Payment</a>
        <?php endif; ?>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Date</th><th>Method</th><th>Reference</th><th>Amount</th></tr></thead>
            <tbody>
            <?php if (empty($payments)): ?>
                <tr><td colspan="4" class="text-center text-muted">No payments recorded</td></tr>
            <?php else: ?>
                <?php foreach ($payments as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['payment_date'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($p['payment_method'] ?? '', ENT_QUOTES, 'UTF-8') ?><?= !empty($p['cheque_number']) ? ' #' . htmlspecialchars($p['cheque_number'], ENT_QUOTES, 'UTF-8') : '' ?></td>
                    <td><?= htmlspecialchars($p['reference'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-green font-semibold"><?= $fmt((float)($p['amount'] ?? 0)) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
