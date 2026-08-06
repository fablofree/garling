<?php $v = $vehicle ?? []; ?>
<div class="page-header">
    <h1 class="page-title"><?= htmlspecialchars($title ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
    <a href="/vehicles" class="btn btn-outline">← Back</a>
</div>

<!-- Datalists for autocomplete -->
<datalist id="makesList">
    <?php foreach ($makes ?? [] as $mk): ?>
        <option value="<?= htmlspecialchars($mk['name'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endforeach; ?>
</datalist>
<datalist id="modelsList">
    <?php foreach ($models ?? [] as $md): ?>
        <option value="<?= htmlspecialchars($md['name'], ENT_QUOTES, 'UTF-8') ?>"
                data-make-id="<?= (int)$md['make_id'] ?>">
    <?php endforeach; ?>
</datalist>
<datalist id="typesList">
    <?php foreach ($types ?? [] as $tp): ?>
        <option value="<?= htmlspecialchars($tp['name'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endforeach; ?>
</datalist>
<datalist id="coloursList">
    <?php foreach ($colours ?? [] as $cl): ?>
        <option value="<?= htmlspecialchars($cl['name'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endforeach; ?>
</datalist>

<div class="card" style="max-width:700px">
    <form method="POST" action="<?= htmlspecialchars($action ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="form-group">
            <label class="form-label required">Customer</label>
            <select name="customer_id" class="form-control" required>
                <option value="">— Select Customer —</option>
                <?php foreach ($customers ?? [] as $c): ?>
                    <option value="<?= (int)$c['id'] ?>"
                        <?= ((int)($v['customer_id'] ?? 0) === (int)$c['id'] || (int)($selectedCustomer ?? 0) === (int)$c['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label required">Registration No.</label>
                <input type="text" name="registration_no" class="form-control"
                       value="<?= htmlspecialchars($v['registration_no'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       style="text-transform:uppercase" required>
            </div>
            <div class="form-group">
                <label class="form-label">Chassis No.</label>
                <input type="text" name="chassis_no" class="form-control"
                       value="<?= htmlspecialchars($v['chassis_no'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <div class="form-grid-3">
            <div class="form-group">
                <label class="form-label">Make</label>
                <div class="combo-input-row">
                    <input type="text" name="make" id="makeInput" class="form-control"
                           list="makesList"
                           value="<?= htmlspecialchars($v['make'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="e.g. Toyota"
                           autocomplete="off">
                    <button type="button" class="btn btn-outline btn-add-attr"
                            data-attr-type="makes" data-attr-target="makeInput"
                            title="Add new make">+</button>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Model</label>
                <div class="combo-input-row">
                    <input type="text" name="model" id="modelInput" class="form-control"
                           list="modelsList"
                           value="<?= htmlspecialchars($v['model'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="e.g. Corolla"
                           autocomplete="off">
                    <button type="button" class="btn btn-outline btn-add-attr"
                            data-attr-type="models" data-attr-target="modelInput"
                            title="Add new model">+</button>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Year</label>
                <input type="number" name="year" class="form-control" min="1950" max="<?= date('Y') + 1 ?>" step="1"
                       value="<?= htmlspecialchars((string)($v['year'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <div class="form-grid-3">
            <div class="form-group">
                <label class="form-label">Vehicle Type</label>
                <div class="combo-input-row">
                    <input type="text" name="vehicle_type" id="typeInput" class="form-control"
                           list="typesList"
                           value="<?= htmlspecialchars($v['vehicle_type'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="e.g. SUV"
                           autocomplete="off">
                    <button type="button" class="btn btn-outline btn-add-attr"
                            data-attr-type="types" data-attr-target="typeInput"
                            title="Add new type">+</button>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Colour</label>
                <div class="combo-input-row">
                    <input type="text" name="colour" id="colourInput" class="form-control"
                           list="coloursList"
                           value="<?= htmlspecialchars($v['colour'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="e.g. Silver"
                           autocomplete="off">
                    <button type="button" class="btn btn-outline btn-add-attr"
                            data-attr-type="colours" data-attr-target="colourInput"
                            title="Add new colour">+</button>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Distance Unit</label>
                <select name="distance_unit" class="form-control" id="distUnitSelect">
                    <option value="km"    <?= ($v['distance_unit'] ?? 'km') === 'km'    ? 'selected' : '' ?>>Kilometres (km)</option>
                    <option value="miles" <?= ($v['distance_unit'] ?? '')   === 'miles' ? 'selected' : '' ?>>Miles</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Servicing Frequency</label>
            <div class="input-with-suffix">
                <input type="number" name="servicing_frequency" class="form-control"
                       value="<?= htmlspecialchars((string)($v['servicing_frequency'] ?? 5000), ENT_QUOTES, 'UTF-8') ?>"
                       min="0" step="1">
                <span class="input-suffix" id="freqUnit"><?= htmlspecialchars($v['distance_unit'] ?? 'km', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="2"><?= htmlspecialchars($v['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= htmlspecialchars($submitLabel ?? 'Save', ENT_QUOTES, 'UTF-8') ?></button>
            <a href="/vehicles" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<!-- Attribute add modal (shared) -->
<div class="attr-modal" id="attrModal" style="display:none">
    <div class="attr-modal-backdrop" id="attrModalBackdrop"></div>
    <div class="attr-modal-content">
        <h3 id="attrModalTitle">Add New</h3>
        <div class="form-group" style="margin-bottom:12px">
            <label class="form-label">Name</label>
            <input type="text" id="attrModalInput" class="form-control" placeholder="Enter name">
        </div>
        <div id="attrMakeGroup" style="display:none;margin-bottom:12px">
            <label class="form-label">Make (optional)</label>
            <select id="attrMakeSelect" class="form-control">
                <option value="">— Any —</option>
                <?php foreach ($makes ?? [] as $mk): ?>
                    <option value="<?= (int)$mk['id'] ?>"><?= htmlspecialchars($mk['name'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <p id="attrModalError" class="text-red" style="display:none;margin-bottom:10px;font-size:13px"></p>
        <div class="form-actions" style="border:none;padding:0;margin:0">
            <button type="button" class="btn btn-primary" id="attrModalSave">Create</button>
            <button type="button" class="btn btn-outline" id="attrModalCancel">Cancel</button>
        </div>
    </div>
</div>

<script>
// ── Distance unit suffix ──────────────────────────────────────────────
(function() {
    var duSel  = document.getElementById('distUnitSelect');
    var fuSpan = document.getElementById('freqUnit');
    if (duSel && fuSpan) {
        fuSpan.textContent = duSel.value;
        duSel.addEventListener('change', function() { fuSpan.textContent = duSel.value; });
    }
})();

// ── Attribute modal ───────────────────────────────────────────────────
var currentAttrType   = '';
var currentAttrTarget = '';

var CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

document.querySelectorAll('.btn-add-attr').forEach(function(btn) {
    btn.addEventListener('click', function() {
        currentAttrType   = btn.getAttribute('data-attr-type');
        currentAttrTarget = btn.getAttribute('data-attr-target');

        var titles = { makes: 'Add Make', models: 'Add Model', types: 'Add Type', colours: 'Add Colour' };
        document.getElementById('attrModalTitle').textContent = titles[currentAttrType] || 'Add New';
        document.getElementById('attrModalInput').value = '';
        document.getElementById('attrModalError').style.display = 'none';
        document.getElementById('attrMakeGroup').style.display = currentAttrType === 'models' ? '' : 'none';
        document.getElementById('attrModal').style.display = 'flex';
        setTimeout(function() { document.getElementById('attrModalInput').focus(); }, 50);
    });
});

function closeAttrModal() {
    document.getElementById('attrModal').style.display = 'none';
}

document.getElementById('attrModalCancel').addEventListener('click', closeAttrModal);
document.getElementById('attrModalBackdrop').addEventListener('click', closeAttrModal);

document.getElementById('attrModalSave').addEventListener('click', function() {
    var name = document.getElementById('attrModalInput').value.trim();
    if (!name) {
        showModalError('Name is required.');
        return;
    }

    var body = new FormData();
    body.append('name', name);
    body.append('_csrf_token', CSRF_TOKEN);
    if (currentAttrType === 'models') {
        body.append('make_id', document.getElementById('attrMakeSelect').value);
    }

    document.getElementById('attrModalSave').disabled = true;

    fetch('/api/vehicle-attributes/' + currentAttrType + '/create', {
        method: 'POST',
        body: body
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        document.getElementById('attrModalSave').disabled = false;
        if (data.success) {
            // Set the input field value
            var targetInput = document.getElementById(currentAttrTarget);
            if (targetInput) targetInput.value = data.name;

            // Add to appropriate datalist
            var dlMap = { makes: 'makesList', models: 'modelsList', types: 'typesList', colours: 'coloursList' };
            var dl = document.getElementById(dlMap[currentAttrType]);
            if (dl) {
                var opt = document.createElement('option');
                opt.value = data.name;
                if (data.make_id) opt.setAttribute('data-make-id', data.make_id);
                dl.appendChild(opt);
            }
            // Also add to make select in modal if a make was created
            if (currentAttrType === 'makes') {
                var ms = document.getElementById('attrMakeSelect');
                var o = document.createElement('option');
                o.value = data.id;
                o.textContent = data.name;
                ms.appendChild(o);
            }
            closeAttrModal();
        } else {
            showModalError(data.error || 'Could not create attribute.');
        }
    })
    .catch(function() {
        document.getElementById('attrModalSave').disabled = false;
        showModalError('Network error. Please try again.');
    });
});

function showModalError(msg) {
    var el = document.getElementById('attrModalError');
    el.textContent = msg;
    el.style.display = '';
}

// Allow Enter key in modal input
document.getElementById('attrModalInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); document.getElementById('attrModalSave').click(); }
});
</script>
