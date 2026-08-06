<div class="page-header">
    <h1 class="page-title">Parts Catalog</h1>
    <p class="page-subtitle">Manage part categories and items for quick lookup on service forms.</p>
</div>

<?php if (($_user['role'] ?? '') === 'admin'): ?>
<!-- Add category (admin only) -->
<div class="card mb-4" style="max-width:500px">
    <div class="card-header"><h2 class="card-title">Add Category</h2></div>
    <form method="POST" action="/catalog/categories/store" style="padding:20px;display:flex;gap:10px;align-items:flex-end">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <div class="form-group" style="flex:1;margin:0">
            <label class="form-label">Category Name</label>
            <input type="text" name="name" class="form-control" required placeholder="e.g. Filters">
        </div>
        <button type="submit" class="btn btn-primary">Add</button>
    </form>
</div>
<?php endif; ?>

<!-- Categories grid -->
<div class="stats-grid" style="grid-template-columns:repeat(auto-fill,minmax(200px,1fr))">
    <?php foreach ($categories ?? [] as $cat): ?>
    <div class="stat-card" style="flex-direction:column;align-items:flex-start;gap:8px;position:relative">
        <a href="/catalog/<?= (int)$cat['id'] ?>" style="font-size:15px;font-weight:600;color:var(--text);text-decoration:none">
            <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>
        </a>
        <span class="text-muted" style="font-size:12px"><?= (int)$cat['item_count'] ?> item<?= (int)$cat['item_count'] !== 1 ? 's' : '' ?></span>
        <?php if (($_user['role'] ?? '') === 'admin'): ?>
        <div style="display:flex;gap:6px;margin-top:4px">
            <a href="/catalog/<?= (int)$cat['id'] ?>" class="btn btn-sm btn-outline">View</a>
            <form method="POST" action="/catalog/categories/<?= (int)$cat['id'] ?>/delete"
                  onsubmit="return confirm('Delete category and all its items?')">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="btn btn-sm btn-danger">Del</button>
            </form>
        </div>
        <?php else: ?>
        <a href="/catalog/<?= (int)$cat['id'] ?>" class="btn btn-sm btn-outline" style="margin-top:4px">View Items</a>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <?php if (empty($categories)): ?>
    <div class="card" style="grid-column:1/-1;padding:32px;text-align:center;color:var(--text-muted)">
        No categories yet. <?= ($_user['role'] ?? '') === 'admin' ? 'Use the form above to add one.' : '' ?>
    </div>
    <?php endif; ?>
</div>
