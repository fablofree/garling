<div class="page-header">
    <h1 class="page-title">Vehicles</h1>
    <a href="/vehicles/create" class="btn btn-primary">+ Register Vehicle</a>
</div>

<div class="card mb-4">
    <form method="GET" action="/vehicles" class="search-form">
        <input type="text" name="search" value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>"
               placeholder="Search by reg no, make, model, chassis..." class="form-control search-input">
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if (!empty($search)): ?>
            <a href="/vehicles" class="btn btn-outline">Clear</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">All Vehicles (<?= count($vehicles ?? []) ?>)</h2>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Reg No</th>
                    <th>Customer</th>
                    <th>Make / Model</th>
                    <th>Type</th>
                    <th>Colour</th>
                    <th>Year</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($vehicles)): ?>
                <tr><td colspan="7" class="text-center text-muted py-8">No vehicles found</td></tr>
            <?php else: ?>
                <?php foreach ($vehicles as $v): ?>
                <tr>
                    <td><a href="/vehicles/<?= (int)$v['id'] ?>" class="link font-semibold"><?= htmlspecialchars($v['registration_no'] ?? '', ENT_QUOTES, 'UTF-8') ?></a></td>
                    <td><a href="/customers/<?= (int)($v['customer_id'] ?? 0) ?>" class="link"><?= htmlspecialchars($v['customer_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></a></td>
                    <td><?= htmlspecialchars(trim(($v['make'] ?? '') . ' ' . ($v['model'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($v['vehicle_type'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($v['colour'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)($v['year'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <div class="action-group">
                            <a href="/vehicles/<?= (int)$v['id'] ?>" class="btn btn-xs btn-outline">View</a>
                            <a href="/vehicles/<?= (int)$v['id'] ?>/edit" class="btn btn-xs btn-outline">Edit</a>
                            <form method="POST" action="/vehicles/<?= (int)$v['id'] ?>/delete" class="inline" onsubmit="return confirm('Delete this vehicle?')">
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
