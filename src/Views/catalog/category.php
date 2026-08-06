<?php $cat = $category ?? []; ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?= htmlspecialchars($cat['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="page-subtitle">Parts catalog — <?= count($items ?? []) ?> item<?= count($items ?? []) !== 1 ? 's' : '' ?></p>
    </div>
    <a href="/catalog" class="btn btn-outline">← Catalog</a>
</div>

<?php if (($_user['role'] ?? '') === 'admin'): ?>
<!-- Add item form -->
<div class="card mb-4">
    <div class="card-header"><h2 class="card-title">Add Item</h2></div>
    <form method="POST" action="/catalog/<?= (int)$cat['id'] ?>/items/store">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <div class="form-grid-3">
            <div class="form-group">
                <label class="form-label required">Name</label>
                <input type="text" name="name" class="form-control" required placeholder="Part name">
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <input type="text" name="description" class="form-control" placeholder="Optional description">
            </div>
            <div class="form-group">
                <label class="form-label">Unit Price</label>
                <div class="input-with-prefix">
                    <span class="input-prefix"><?= htmlspecialchars($_app['currency']['symbol'] ?? 'Rs', ENT_QUOTES, 'UTF-8') ?></span>
                    <input type="number" name="unit_price" class="form-control" min="0" step="0.01" value="0" placeholder="0.00">
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Add Item</button>
        </div>
    </form>
</div>
<?php endif; ?>

<!-- Items table -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Items</h2>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th class="text-right">Unit Price</th>
                    <?php if (($_user['role'] ?? '') === 'admin'): ?>
                    <th style="width:120px">Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($items ?? [] as $item): ?>
            <tr>
                <td class="font-semibold"><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></td>
                <td class="text-muted"><?= htmlspecialchars($item['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td class="text-right">
                    <?= htmlspecialchars($_app['currency']['symbol'] ?? 'Rs', ENT_QUOTES, 'UTF-8') ?>
                    <?= number_format((float)($item['unit_price'] ?? 0), 2) ?>
                </td>
                <?php if (($_user['role'] ?? '') === 'admin'): ?>
                <td>
                    <div class="action-group">
                        <button type="button" class="btn btn-xs btn-outline"
                                onclick="openEditItem(<?= (int)$item['id'] ?>, '<?= htmlspecialchars(addslashes($item['name']), ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars(addslashes($item['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?>', '<?= (float)$item['unit_price'] ?>')">
                            Edit
                        </button>
                        <form method="POST" action="/catalog/items/<?= (int)$item['id'] ?>/delete"
                              onsubmit="return confirm('Delete this item?')" style="display:inline">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="btn btn-xs btn-danger">Del</button>
                        </form>
                    </div>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
            <tr><td colspan="<?= ($_user['role'] ?? '') === 'admin' ? 4 : 3 ?>" class="text-center text-muted py-8">
                No items in this category yet.
            </td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (($_user['role'] ?? '') === 'admin'): ?>
<!-- Edit item modal -->
<div class="attr-modal" id="editItemModal" style="display:none">
    <div class="attr-modal-backdrop" onclick="document.getElementById('editItemModal').style.display='none'"></div>
    <div class="attr-modal-content" style="width:420px">
        <h3>Edit Item</h3>
        <form method="POST" id="editItemForm">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="category_id" value="<?= (int)$cat['id'] ?>">
            <div class="form-group">
                <label class="form-label required">Name</label>
                <input type="text" name="name" id="editItemName" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <input type="text" name="description" id="editItemDesc" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Unit Price</label>
                <input type="number" name="unit_price" id="editItemPrice" class="form-control" min="0" step="0.01">
            </div>
            <div class="form-actions" style="border:none;padding:0;margin:0">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-outline" onclick="document.getElementById('editItemModal').style.display='none'">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditItem(id, name, description, price) {
    document.getElementById('editItemForm').action = '/catalog/items/' + id + '/update';
    document.getElementById('editItemName').value  = name;
    document.getElementById('editItemDesc').value  = description;
    document.getElementById('editItemPrice').value = price;
    document.getElementById('editItemModal').style.display = 'flex';
}
</script>
<?php endif; ?>
