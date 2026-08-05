<?php
$c   = $customer ?? [];
$cfg = $_app ?? [];
$cur = $cfg['currency']['symbol'] ?? 'Rs';
$fmt = fn(float $v) => $cur . ' ' . number_format($v, 2);
?>
<div class="page-header">
    <h1 class="page-title"><?= htmlspecialchars($c['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
    <div class="action-group">
        <a href="/customers/<?= (int)($c['id'] ?? 0) ?>/edit" class="btn btn-outline">Edit</a>
        <a href="/vehicles/create?customer_id=<?= (int)($c['id'] ?? 0) ?>" class="btn btn-outline">+ Vehicle</a>
        <a href="/services/create?customer_id=<?= (int)($c['id'] ?? 0) ?>" class="btn btn-primary">+ Service Entry</a>
    </div>
</div>

<!-- Customer details -->
<div class="details-grid mb-6">
    <div class="card">
        <div class="card-header"><h2 class="card-title">Contact Details</h2></div>
        <div class="detail-list">
            <div class="detail-row"><span class="detail-label">Mobile</span><span><?= htmlspecialchars($c['tel_mobile'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="detail-row"><span class="detail-label">Home</span><span><?= htmlspecialchars($c['tel_home'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="detail-row"><span class="detail-label">Office</span><span><?= htmlspecialchars($c['tel_office'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="detail-row"><span class="detail-label">Fax</span><span><?= htmlspecialchars($c['fax'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="detail-row"><span class="detail-label">Email</span><span><?= htmlspecialchars($c['email'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="detail-row"><span class="detail-label">Address</span><span><?= nl2br(htmlspecialchars($c['address'] ?? '—', ENT_QUOTES, 'UTF-8')) ?></span></div>
            <?php if (!empty($c['notes'])): ?>
            <div class="detail-row"><span class="detail-label">Notes</span><span><?= nl2br(htmlspecialchars($c['notes'], ENT_QUOTES, 'UTF-8')) ?></span></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Vehicles -->
<div class="card mb-6">
    <div class="card-header">
        <h2 class="card-title">Vehicles (<?= count($vehicles ?? []) ?>)</h2>
        <a href="/vehicles/create?customer_id=<?= (int)($c['id'] ?? 0) ?>" class="btn btn-sm btn-primary">+ Add Vehicle</a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr><th>Reg No</th><th>Make / Model</th><th>Type</th><th>Colour</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php if (empty($vehicles)): ?>
                <tr><td colspan="5" class="text-center text-muted">No vehicles registered</td></tr>
            <?php else: ?>
                <?php foreach ($vehicles as $v): ?>
                <tr>
                    <td><a href="/vehicles/<?= (int)$v['id'] ?>" class="link font-semibold"><?= htmlspecialchars($v['registration_no'] ?? '', ENT_QUOTES, 'UTF-8') ?></a></td>
                    <td><?= htmlspecialchars(trim(($v['make'] ?? '') . ' ' . ($v['model'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($v['vehicle_type'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($v['colour'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <a href="/vehicles/<?= (int)$v['id'] ?>" class="btn btn-xs btn-outline">View</a>
                        <a href="/vehicles/<?= (int)$v['id'] ?>/edit" class="btn btn-xs btn-outline">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
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
                <tr><th>Ref</th><th>Date</th><th>Vehicle</th><th>Type</th><th>Total</th><th>Status</th><th></th></tr>
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
                    <td><?= htmlspecialchars($se['registration_no'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="badge badge-<?= strtolower($se['entry_type'] ?? 'invoice') ?>"><?= htmlspecialchars($se['entry_type'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></td>
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
