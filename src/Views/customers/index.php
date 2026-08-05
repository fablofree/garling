<div class="page-header">
    <h1 class="page-title">Customers</h1>
    <a href="/customers/create" class="btn btn-primary">+ New Customer</a>
</div>

<!-- Search -->
<div class="card mb-4">
    <form method="GET" action="/customers" class="search-form">
        <input type="text" name="search" value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>"
               placeholder="Search by name, email or phone..." class="form-control search-input">
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if (!empty($search)): ?>
            <a href="/customers" class="btn btn-outline">Clear</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <?php if (!empty($search)): ?>
                Search results for "<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
            <?php else: ?>
                All Customers (<?= count($customers ?? []) ?>)
            <?php endif; ?>
        </h2>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($customers)): ?>
                <tr><td colspan="5" class="text-center text-muted py-8">No customers found</td></tr>
            <?php else: ?>
                <?php foreach ($customers as $c): ?>
                <tr>
                    <td><a href="/customers/<?= (int)$c['id'] ?>" class="link font-semibold"><?= htmlspecialchars($c['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></a></td>
                    <td><?= htmlspecialchars($c['tel_mobile'] ?? $c['tel_home'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($c['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-muted"><?= htmlspecialchars(mb_substr($c['address'] ?? '', 0, 50), ENT_QUOTES, 'UTF-8') ?><?= mb_strlen($c['address'] ?? '') > 50 ? '…' : '' ?></td>
                    <td>
                        <div class="action-group">
                            <a href="/customers/<?= (int)$c['id'] ?>" class="btn btn-xs btn-outline">View</a>
                            <a href="/customers/<?= (int)$c['id'] ?>/edit" class="btn btn-xs btn-outline">Edit</a>
                            <form method="POST" action="/customers/<?= (int)$c['id'] ?>/delete" class="inline" onsubmit="return confirm('Delete this customer?')">
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
