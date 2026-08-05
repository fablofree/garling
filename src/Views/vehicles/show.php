<?php
$v   = $vehicle ?? [];
$cfg = $_app ?? [];
$cur = $cfg['currency']['symbol'] ?? 'Rs';
$fmt = fn(float $val) => $cur . ' ' . number_format($val, 2);
?>
<div class="page-header">
    <h1 class="page-title"><?= htmlspecialchars($v['registration_no'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
    <div class="action-group">
        <a href="/vehicles/<?= (int)($v['id'] ?? 0) ?>/edit" class="btn btn-outline">Edit</a>
        <a href="/services/create?vehicle_id=<?= (int)($v['id'] ?? 0) ?>&customer_id=<?= (int)($v['customer_id'] ?? 0) ?>" class="btn btn-primary">+ Service Entry</a>
    </div>
</div>

<div class="details-grid mb-6">
    <div class="card">
        <div class="card-header"><h2 class="card-title">Vehicle Details</h2></div>
        <div class="detail-list">
            <div class="detail-row"><span class="detail-label">Owner</span><span><a href="/customers/<?= (int)($v['customer_id'] ?? 0) ?>" class="link"><?= htmlspecialchars($v['customer_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></a></span></div>
            <div class="detail-row"><span class="detail-label">Make</span><span><?= htmlspecialchars($v['make'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="detail-row"><span class="detail-label">Model</span><span><?= htmlspecialchars($v['model'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="detail-row"><span class="detail-label">Year</span><span><?= htmlspecialchars((string)($v['year'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="detail-row"><span class="detail-label">Type</span><span><?= htmlspecialchars($v['vehicle_type'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="detail-row"><span class="detail-label">Colour</span><span><?= htmlspecialchars($v['colour'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="detail-row"><span class="detail-label">Chassis No.</span><span><?= htmlspecialchars($v['chassis_no'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="detail-row"><span class="detail-label">Distance Unit</span><span><?= htmlspecialchars(strtoupper($v['distance_unit'] ?? 'km'), ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="detail-row"><span class="detail-label">Service Freq.</span><span><?= htmlspecialchars(number_format((int)($v['servicing_frequency'] ?? 0)) . ' ' . ($v['distance_unit'] ?? 'km'), ENT_QUOTES, 'UTF-8') ?></span></div>
            <?php if (!empty($v['notes'])): ?>
            <div class="detail-row"><span class="detail-label">Notes</span><span><?= nl2br(htmlspecialchars($v['notes'], ENT_QUOTES, 'UTF-8')) ?></span></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Service history -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Service History (<?= count($services ?? []) ?>)</h2>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr><th>Ref</th><th>Date</th><th>Odometer</th><th>Type</th><th>Total</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
            <?php if (empty($services)): ?>
                <tr><td colspan="7" class="text-center text-muted">No service history</td></tr>
            <?php else: ?>
                <?php foreach ($services as $se): ?>
                <?php
                    $total  = (float)($se['total_cost'] ?? 0);
                    $paid   = (float)($se['total_paid'] ?? 0);
                    $bal    = $total - $paid;
                    $status = $bal <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
                ?>
                <tr>
                    <td><a href="/services/<?= (int)$se['id'] ?>" class="link"><?= str_pad((string)$se['id'], 6, '0', STR_PAD_LEFT) ?></a></td>
                    <td><?= htmlspecialchars($se['entry_date'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= $se['odometer'] ? number_format((int)$se['odometer']) . ' ' . ($v['distance_unit'] ?? 'km') : '—' ?></td>
                    <td><span class="badge badge-<?= strtolower($se['entry_type'] ?? '') ?>"><?= htmlspecialchars($se['entry_type'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td><?= $fmt($total) ?></td>
                    <td><span class="badge badge-<?= $status ?>"><?= ucfirst($status) ?></span></td>
                    <td><a href="/services/<?= (int)$se['id'] ?>" class="btn btn-xs btn-outline">View</a></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
