<?php
if (($_user['role'] ?? '') !== 'admin') {
    echo '<div class="alert alert-error">Access denied. Admin only.</div>';
    return;
}
// Active tab from URL hash (JS controlled; default shows makes)
?>
<div class="page-header">
    <h1 class="page-title">Vehicle Attributes</h1>
    <p class="page-subtitle">Manage makes, models, types and colours used in vehicle registration.</p>
</div>

<!-- Tab navigation -->
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap">
    <button class="btn attr-tab-btn active" data-tab="makes">Makes</button>
    <button class="btn attr-tab-btn" data-tab="models">Models</button>
    <button class="btn attr-tab-btn" data-tab="types">Types</button>
    <button class="btn attr-tab-btn" data-tab="colours">Colours</button>
</div>

<!-- ── MAKES ─────────────────────────────────────────────────────────── -->
<div id="tab-makes" class="attr-tab-panel">
    <div class="card mb-4">
        <div class="card-header"><h2 class="card-title">Add Make</h2></div>
        <form method="POST" action="/vehicle-attributes/makes/store" style="padding:20px;display:flex;gap:10px;align-items:flex-end">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <div class="form-group" style="flex:1;margin:0">
                <label class="form-label">Make Name</label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. Toyota">
            </div>
            <button type="submit" class="btn btn-primary">Add</button>
        </form>
    </div>

    <div class="card">
        <div class="card-header"><h2 class="card-title">Makes (<?= count($makes ?? []) ?>)</h2></div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th>Name</th><th style="width:180px">Actions</th></tr></thead>
                <tbody>
                <?php foreach ($makes ?? [] as $m): ?>
                <tr>
                    <td>
                        <form method="POST" action="/vehicle-attributes/makes/<?= (int)$m['id'] ?>/update"
                              style="display:flex;gap:8px;align-items:center">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <input type="text" name="name" class="form-control form-control-sm"
                                   value="<?= htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8') ?>"
                                   style="max-width:220px">
                            <button type="submit" class="btn btn-sm btn-outline">Save</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="/vehicle-attributes/makes/<?= (int)$m['id'] ?>/delete"
                              onsubmit="return confirm('Delete this make?')">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($makes)): ?>
                    <tr><td colspan="2" class="text-center text-muted py-6">No makes yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── MODELS ────────────────────────────────────────────────────────── -->
<div id="tab-models" class="attr-tab-panel" style="display:none">
    <div class="card mb-4">
        <div class="card-header"><h2 class="card-title">Add Model</h2></div>
        <form method="POST" action="/vehicle-attributes/models/store" style="padding:20px;display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <div class="form-group" style="margin:0">
                <label class="form-label">Make</label>
                <select name="make_id" class="form-control">
                    <option value="">— Any —</option>
                    <?php foreach ($makes ?? [] as $mk): ?>
                        <option value="<?= (int)$mk['id'] ?>"><?= htmlspecialchars($mk['name'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="flex:1;margin:0;min-width:180px">
                <label class="form-label">Model Name</label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. Corolla">
            </div>
            <button type="submit" class="btn btn-primary">Add</button>
        </form>
    </div>

    <div class="card">
        <div class="card-header"><h2 class="card-title">Models (<?= count($models ?? []) ?>)</h2></div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th>Name</th><th>Make</th><th style="width:120px">Actions</th></tr></thead>
                <tbody>
                <?php foreach ($models ?? [] as $md): ?>
                <tr>
                    <td><?= htmlspecialchars($md['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="badge badge-category"><?= htmlspecialchars($md['make_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td>
                        <form method="POST" action="/vehicle-attributes/models/<?= (int)$md['id'] ?>/delete"
                              onsubmit="return confirm('Delete this model?')">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($models)): ?>
                    <tr><td colspan="3" class="text-center text-muted py-6">No models yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── TYPES ─────────────────────────────────────────────────────────── -->
<div id="tab-types" class="attr-tab-panel" style="display:none">
    <div class="card mb-4">
        <div class="card-header"><h2 class="card-title">Add Type</h2></div>
        <form method="POST" action="/vehicle-attributes/types/store" style="padding:20px;display:flex;gap:10px;align-items:flex-end">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <div class="form-group" style="flex:1;margin:0">
                <label class="form-label">Type Name</label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. SUV">
            </div>
            <button type="submit" class="btn btn-primary">Add</button>
        </form>
    </div>

    <div class="card">
        <div class="card-header"><h2 class="card-title">Types (<?= count($types ?? []) ?>)</h2></div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th>Name</th><th style="width:180px">Actions</th></tr></thead>
                <tbody>
                <?php foreach ($types ?? [] as $t): ?>
                <tr>
                    <td>
                        <form method="POST" action="/vehicle-attributes/types/<?= (int)$t['id'] ?>/update"
                              style="display:flex;gap:8px;align-items:center">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <input type="text" name="name" class="form-control"
                                   value="<?= htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8') ?>"
                                   style="max-width:220px">
                            <button type="submit" class="btn btn-sm btn-outline">Save</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="/vehicle-attributes/types/<?= (int)$t['id'] ?>/delete"
                              onsubmit="return confirm('Delete this type?')">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($types)): ?>
                    <tr><td colspan="2" class="text-center text-muted py-6">No types yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── COLOURS ───────────────────────────────────────────────────────── -->
<div id="tab-colours" class="attr-tab-panel" style="display:none">
    <div class="card mb-4">
        <div class="card-header"><h2 class="card-title">Add Colour</h2></div>
        <form method="POST" action="/vehicle-attributes/colours/store" style="padding:20px;display:flex;gap:10px;align-items:flex-end">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <div class="form-group" style="flex:1;margin:0">
                <label class="form-label">Colour Name</label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. Silver">
            </div>
            <button type="submit" class="btn btn-primary">Add</button>
        </form>
    </div>

    <div class="card">
        <div class="card-header"><h2 class="card-title">Colours (<?= count($colours ?? []) ?>)</h2></div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th>Name</th><th style="width:180px">Actions</th></tr></thead>
                <tbody>
                <?php foreach ($colours ?? [] as $c): ?>
                <tr>
                    <td>
                        <form method="POST" action="/vehicle-attributes/colours/<?= (int)$c['id'] ?>/update"
                              style="display:flex;gap:8px;align-items:center">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <input type="text" name="name" class="form-control"
                                   value="<?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?>"
                                   style="max-width:220px">
                            <button type="submit" class="btn btn-sm btn-outline">Save</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="/vehicle-attributes/colours/<?= (int)$c['id'] ?>/delete"
                              onsubmit="return confirm('Delete this colour?')">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($colours)): ?>
                    <tr><td colspan="2" class="text-center text-muted py-6">No colours yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
(function() {
    var buttons = document.querySelectorAll('.attr-tab-btn');
    var panels  = document.querySelectorAll('.attr-tab-panel');

    function showTab(name) {
        buttons.forEach(function(b) {
            b.classList.toggle('active', b.getAttribute('data-tab') === name);
            b.classList.toggle('btn-primary', b.getAttribute('data-tab') === name);
            b.classList.toggle('btn-outline', b.getAttribute('data-tab') !== name);
        });
        panels.forEach(function(p) {
            p.style.display = p.id === 'tab-' + name ? '' : 'none';
        });
        location.hash = name;
    }

    buttons.forEach(function(b) {
        b.addEventListener('click', function() { showTab(b.getAttribute('data-tab')); });
    });

    // Initialise from hash
    var hash = location.hash.replace('#', '');
    var valid = ['makes', 'models', 'types', 'colours'];
    showTab(valid.includes(hash) ? hash : 'makes');
})();
</script>
