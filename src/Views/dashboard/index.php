<?php
$s       = $stats ?? [];
$cfg     = $_app ?? [];
$cur     = $cfg['currency']['symbol'] ?? 'Rs';
$fmt     = fn(float $v) => $cur . ' ' . number_format($v, 2);
$fmtInt  = fn(float $v) => number_format($v, 0);
?>
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle"><?= htmlspecialchars($s['current_month'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
</div>

<!-- Stat cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        </div>
        <div class="stat-body">
            <div class="stat-value"><?= $fmtInt((float)($s['total_customers'] ?? 0)) ?></div>
            <div class="stat-label">Total Customers</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-purple">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 6v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <div class="stat-body">
            <div class="stat-value"><?= $fmtInt((float)($s['total_vehicles'] ?? 0)) ?></div>
            <div class="stat-label">Registered Vehicles</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-red">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <div class="stat-body">
            <div class="stat-value"><?= $fmt((float)($s['outstanding_debt'] ?? 0)) ?></div>
            <div class="stat-label">Outstanding Balance</div>
        </div>
    </div>
</div>

<!-- Two columns -->
<div class="dashboard-grid">

    <!-- Recent service entries -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Recent Service Entries</h2>
            <a href="/services/create" class="btn btn-sm btn-primary">+ New</a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Vehicle</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($s['recent_entries'])): ?>
                    <tr><td colspan="6" class="text-center text-muted">No entries yet</td></tr>
                <?php else: ?>
                    <?php foreach ($s['recent_entries'] as $e): ?>
                    <?php
                        $totalCost = (float)($e['total_cost'] ?? 0);
                        $totalPaid = (float)($e['total_paid'] ?? 0);
                        $balance   = $totalCost - $totalPaid;
                        $status    = $balance <= 0 ? 'paid' : ($totalPaid > 0 ? 'partial' : 'unpaid');
                    ?>
                    <tr>
                        <td><a href="/services/<?= (int)$e['id'] ?>" class="link"><?= str_pad((string)$e['id'], 6, '0', STR_PAD_LEFT) ?></a></td>
                        <td><?= htmlspecialchars($e['entry_date'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($e['customer_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($e['registration_no'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= $fmt($totalCost) ?></td>
                        <td><span class="badge badge-<?= $status ?>"><?= ucfirst($status) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <a href="/services" class="link">View all entries →</a>
        </div>
    </div>

    <!-- Outstanding invoices / Debtors -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Outstanding Balances</h2>
            <a href="/reports" class="btn btn-sm btn-outline">Reports</a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Balance</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($s['debtors'])): ?>
                    <tr><td colspan="4" class="text-center text-muted">No outstanding balances</td></tr>
                <?php else: ?>
                    <?php foreach (array_slice($s['debtors'], 0, 8) as $d): ?>
                    <tr>
                        <td><a href="/customers/<?= (int)$d['id'] ?>" class="link"><?= htmlspecialchars($d['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></a></td>
                        <td><?= htmlspecialchars($d['tel_mobile'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="text-red font-semibold"><?= $fmt((float)($d['balance'] ?? 0)) ?></td>
                        <td>
                            <a href="/customers/<?= (int)$d['id'] ?>" class="btn btn-xs btn-outline">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($s['debtors'])): ?>
        <div class="card-footer">
            <strong>Total Outstanding: <?= $fmt((float)($s['outstanding_debt'] ?? 0)) ?></strong>
        </div>
        <?php endif; ?>
    </div>

</div>
