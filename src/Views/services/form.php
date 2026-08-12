<?php
$e          = $entry ?? [];
$isEdit     = !empty($e);
$selVehicle = (int)($e['vehicle_id'] ?? $preselect_vehicle ?? 0);
$selCust    = (int)($e['customer_id'] ?? $preselect_customer ?? 0);
$currSymbol = htmlspecialchars($_app['currency']['symbol'] ?? 'Rs', ENT_QUOTES, 'UTF-8');
$isQuot     = isset($e['is_quotation']) && $e['is_quotation'] == 1;
$isComp     = isset($e['is_completed']) && $e['is_completed'] == 1;
?>
<div class="page-header">
    <h1 class="page-title"><?= htmlspecialchars($title ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
    <a href="/services" class="btn btn-outline">← Back</a>
</div>

<form method="POST" action="<?= htmlspecialchars($action ?? '', ENT_QUOTES, 'UTF-8') ?>" id="serviceForm">
    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <!-- Header info -->
    <div class="card mb-4">
        <div class="card-header"><h2 class="card-title">Entry Details</h2></div>
        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label required">Entry Date</label>
                <input type="date" name="entry_date" class="form-control"
                       value="<?= htmlspecialchars($e['entry_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Delivery Date</label>
                <input type="date" name="delivery_date" class="form-control"
                       value="<?= htmlspecialchars($e['delivery_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label required">Customer</label>
                <select name="customer_id" class="form-control" id="customerSelect" required>
                    <option value="">— Select Customer —</option>
                    <?php foreach ($customers ?? [] as $c): ?>
                        <option value="<?= (int)$c['id'] ?>"
                            data-customer-id="<?= (int)$c['id'] ?>"
                            <?= $selCust === (int)$c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label required">Vehicle</label>
                <select name="vehicle_id" class="form-control" id="vehicleSelect" required>
                    <option value="">— Select Vehicle —</option>
                    <?php foreach ($vehicles ?? [] as $v): ?>
                        <option value="<?= (int)$v['id'] ?>"
                            data-customer-id="<?= (int)$v['customer_id'] ?>"
                            data-freq="<?= (int)($v['servicing_frequency'] ?? 0) ?>"
                            data-unit="<?= htmlspecialchars($v['distance_unit'] ?? 'km', ENT_QUOTES, 'UTF-8') ?>"
                            <?= $selVehicle === (int)$v['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($v['registration_no'] . ' — ' . trim(($v['make'] ?? '') . ' ' . ($v['model'] ?? '')), ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Odometer Reading</label>
                <input type="number" name="odometer" class="form-control" id="odoInput" min="0" step="1"
                       value="<?= htmlspecialchars((string)($e['odometer'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group" id="nextServicingGroup" style="display:<?= $isQuot ? 'none' : 'block' ?>">
                <label class="form-label">Next Servicing At</label>
                <input type="number" name="next_servicing" class="form-control" id="nextServInput" min="0" step="1"
                       value="<?= htmlspecialchars((string)($e['next_servicing'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Remarks</label>
            <textarea name="remarks" class="form-control" rows="3"><?= htmlspecialchars($e['remarks'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>
    </div>

    <!-- Spare parts -->
    <div class="card mb-4">
        <div class="card-header">
            <h2 class="card-title">Spare Parts</h2>
            <button type="button" class="btn btn-sm btn-outline" id="addPartBtn">+ Add Row</button>
        </div>
        <div class="table-responsive">
            <table class="table line-items-table" id="partsTable">
                <thead>
                    <tr>
                        <th style="width:75%">Description</th>
                        <th style="width:20%">Amount</th>
                        <th style="width:40px"></th>
                    </tr>
                </thead>
                <tbody id="partsBody">
                <?php
                $partsData = $spare_parts ?? [];
                if (empty($partsData)) {
                    $partsData = [['description' => '', 'amount' => '']];
                }
                foreach ($partsData as $p):
                ?>
                <tr class="line-item-row">
                    <td style="position:relative">
                        <div style="display:flex;gap:4px;align-items:center">
                            <input type="text" name="parts_description[]" class="form-control desc-input"
                                   value="<?= htmlspecialchars($p['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   placeholder="Description" autocomplete="off">
                            <button type="button" class="btn btn-xs btn-outline catalog-pick-btn" title="Search catalog"
                                    style="flex-shrink:0;padding:4px 7px">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            </button>
                        </div>
                        <div class="catalog-popup" style="display:none"></div>
                    </td>
                    <td>
                        <input type="number" name="parts_amount[]" class="form-control amount-input"
                               value="<?= htmlspecialchars((string)($p['amount'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                               min="0" step="0.01" placeholder="0.00">
                    </td>
                    <td><button type="button" class="btn btn-xs btn-danger remove-row">×</button></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="section-total">Parts Subtotal: <strong id="partsTotalDisplay">0.00</strong></div>
    </div>

    <!-- Repairs / Labour -->
    <div class="card mb-4">
        <div class="card-header">
            <h2 class="card-title">Repairs / Labour</h2>
            <button type="button" class="btn btn-sm btn-outline" id="addRepairBtn">+ Add Row</button>
        </div>
        <div class="table-responsive">
            <table class="table line-items-table" id="repairsTable">
                <thead>
                    <tr>
                        <th style="width:75%">Description</th>
                        <th style="width:20%">Amount</th>
                        <th style="width:40px"></th>
                    </tr>
                </thead>
                <tbody id="repairsBody">
                <?php
                $repairsData = $repairs ?? [];
                if (empty($repairsData)) {
                    $repairsData = [['description' => '', 'amount' => '']];
                }
                foreach ($repairsData as $r):
                ?>
                <tr class="line-item-row">
                    <td style="position:relative">
                        <div style="display:flex;gap:4px;align-items:center">
                            <input type="text" name="repairs_description[]" class="form-control desc-input"
                                   value="<?= htmlspecialchars($r['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   placeholder="Description" autocomplete="off">
                            <button type="button" class="btn btn-xs btn-outline catalog-pick-btn" title="Search catalog"
                                    style="flex-shrink:0;padding:4px 7px">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            </button>
                        </div>
                        <div class="catalog-popup" style="display:none"></div>
                    </td>
                    <td>
                        <input type="number" name="repairs_amount[]" class="form-control amount-input"
                               value="<?= htmlspecialchars((string)($r['amount'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                               min="0" step="0.01" placeholder="0.00">
                    </td>
                    <td><button type="button" class="btn btn-xs btn-danger remove-row">×</button></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="section-total">Labour Subtotal: <strong id="repairsTotalDisplay">0.00</strong></div>
    </div>

    <!-- Totals -->
    <div class="card mb-4">
        <div class="card-header"><h2 class="card-title">Totals</h2></div>
        <div class="totals-section">
            <div class="totals-grid">
                <div class="form-group">
                    <label class="form-label">Discount (<?= $currSymbol ?>)</label>
                    <input type="number" name="discount_amount" id="discountInput" class="form-control"
                           value="<?= htmlspecialchars((string)($e['discount_amount'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>"
                           min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label class="form-label">VAT %</label>
                    <input type="number" name="vat_percent" id="vatInput" class="form-control"
                           value="<?= htmlspecialchars((string)($e['vat_percent'] ?? $default_vat ?? 0), ENT_QUOTES, 'UTF-8') ?>"
                           min="0" step="0.01">
                </div>
            </div>

            <div class="form-group" style="padding:0 4px 12px">
                <label class="form-label">Type</label>
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_quotation" value="1"
                               <?= $isQuot ? 'checked' : '' ?>
                               id="isQuotation">
                        Quotation (not Invoice)
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_completed" value="1"
                               <?= $isComp ? 'checked' : '' ?>>
                        Completed
                    </label>
                </div>
            </div>

            <div class="totals-summary">
                <div class="total-row"><span>Parts Total</span><span id="summaryParts"><?= $currSymbol ?> 0.00</span></div>
                <div class="total-row"><span>Labour Total</span><span id="summaryLabour"><?= $currSymbol ?> 0.00</span></div>
                <div class="total-row"><span>Discount</span><span id="summaryDiscount"><?= $currSymbol ?> 0.00</span></div>
                <div class="total-row"><span>Subtotal</span><span id="summarySubtotal"><?= $currSymbol ?> 0.00</span></div>
                <div class="total-row"><span>VAT</span><span id="summaryVat"><?= $currSymbol ?> 0.00</span></div>
                <div class="total-row total-row-grand"><span>TOTAL</span><span id="summaryTotal"><?= $currSymbol ?> 0.00</span></div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary btn-lg"><?= htmlspecialchars($submitLabel ?? 'Save', ENT_QUOTES, 'UTF-8') ?></button>
        <a href="/services" class="btn btn-outline">Cancel</a>
    </div>
</form>

<script>
var CURR = '<?= $currSymbol ?>';

// ── Vehicle / Customer sync + next servicing auto-calc ──────────────
function calcNextServicing() {
    var odo  = parseInt(document.getElementById('odoInput').value, 10);
    if (isNaN(odo)) return;
    var vSel = document.getElementById('vehicleSelect');
    var opt  = vSel.options[vSel.selectedIndex];
    var freq = parseInt(opt.getAttribute('data-freq') || '0', 10);
    if (freq > 0) {
        var nextEl = document.getElementById('nextServInput');
        if (nextEl && !nextEl.value) {
            nextEl.value = odo + freq;
        }
    }
}

document.getElementById('odoInput').addEventListener('blur', calcNextServicing);

document.getElementById('vehicleSelect').addEventListener('change', function() {
    var opt    = this.options[this.selectedIndex];
    var custId = opt.getAttribute('data-customer-id');
    if (custId) {
        document.getElementById('customerSelect').value = custId;
    }
    var odo  = parseInt(document.getElementById('odoInput').value, 10);
    var freq = parseInt(opt.getAttribute('data-freq') || '0', 10);
    if (!isNaN(odo) && freq > 0) {
        var nextEl = document.getElementById('nextServInput');
        if (nextEl) nextEl.value = odo + freq;
    }
});

document.getElementById('customerSelect').addEventListener('change', function() {
    var custId = this.value;
    var vs = document.getElementById('vehicleSelect');
    for (var i = 0; i < vs.options.length; i++) {
        var o = vs.options[i];
        if (o.value === '') { o.style.display = ''; continue; }
        o.style.display = (!custId || o.getAttribute('data-customer-id') === custId) ? '' : 'none';
    }
    vs.value = '';
});

// Toggle next servicing group when quotation checkbox changes
document.getElementById('isQuotation').addEventListener('change', function() {
    var grp = document.getElementById('nextServicingGroup');
    if (grp) grp.style.display = this.checked ? 'none' : 'block';
});

// ── Line items calculation ──────────────────────────────────────────
function recalcAll() {
    var partsTotal  = 0;
    var labourTotal = 0;

    document.querySelectorAll('#partsBody .amount-input').forEach(function(i) { partsTotal += parseFloat(i.value) || 0; });
    document.querySelectorAll('#repairsBody .amount-input').forEach(function(i) { labourTotal += parseFloat(i.value) || 0; });

    var discount  = parseFloat(document.getElementById('discountInput')?.value) || 0;
    var vatPct    = parseFloat(document.getElementById('vatInput')?.value) || 0;
    var subtotal  = Math.max(0, partsTotal + labourTotal - discount);
    var vatAmount = subtotal * vatPct / 100;
    var total     = subtotal + vatAmount;

    document.getElementById('partsTotalDisplay').textContent   = partsTotal.toFixed(2);
    document.getElementById('repairsTotalDisplay').textContent = labourTotal.toFixed(2);
    document.getElementById('summaryParts').textContent    = CURR + ' ' + partsTotal.toFixed(2);
    document.getElementById('summaryLabour').textContent   = CURR + ' ' + labourTotal.toFixed(2);
    document.getElementById('summaryDiscount').textContent = CURR + ' ' + discount.toFixed(2);
    document.getElementById('summarySubtotal').textContent = CURR + ' ' + subtotal.toFixed(2);
    document.getElementById('summaryVat').textContent      = CURR + ' ' + vatAmount.toFixed(2);
    document.getElementById('summaryTotal').textContent    = CURR + ' ' + total.toFixed(2);
}

function makeRow(prefix, bodyId) {
    var tr = document.createElement('tr');
    tr.className = 'line-item-row';
    tr.innerHTML =
        '<td style="position:relative">' +
            '<div style="display:flex;gap:4px;align-items:center">' +
                '<input type="text" name="' + prefix + '_description[]" class="form-control desc-input" placeholder="Description" autocomplete="off">' +
                '<button type="button" class="btn btn-xs btn-outline catalog-pick-btn" title="Search catalog" style="flex-shrink:0;padding:4px 7px">' +
                    '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>' +
                '</button>' +
            '</div>' +
            '<div class="catalog-popup" style="display:none"></div>' +
        '</td>' +
        '<td><input type="number" name="' + prefix + '_amount[]" class="form-control amount-input" min="0" step="0.01" placeholder="0.00"></td>' +
        '<td><button type="button" class="btn btn-xs btn-danger remove-row">×</button></td>';
    return tr;
}

document.getElementById('addPartBtn').addEventListener('click', function() {
    document.getElementById('partsBody').appendChild(makeRow('parts', 'partsBody'));
});
document.getElementById('addRepairBtn').addEventListener('click', function() {
    document.getElementById('repairsBody').appendChild(makeRow('repairs', 'repairsBody'));
});

// Auto-add row when amount input in last row gets focus
function setupAutoAddRow(bodyId, prefix) {
    var tbody = document.getElementById(bodyId);
    tbody.addEventListener('focus', function(e) {
        if (!e.target.classList.contains('amount-input')) return;
        var rows = tbody.querySelectorAll('tr.line-item-row');
        var lastRow = rows[rows.length - 1];
        if (lastRow && lastRow.contains(e.target)) {
            tbody.appendChild(makeRow(prefix, bodyId));
        }
    }, true);
}
setupAutoAddRow('partsBody', 'parts');
setupAutoAddRow('repairsBody', 'repairs');

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-row')) {
        var row  = e.target.closest('tr');
        var body = row.parentElement;
        if (body.querySelectorAll('tr').length > 1) {
            row.remove();
            recalcAll();
        } else {
            row.querySelectorAll('input').forEach(function(inp) { if (!inp.readOnly) inp.value = ''; });
            recalcAll();
        }
    }
    if (!e.target.closest('.catalog-pick-btn') && !e.target.closest('.catalog-popup')) {
        document.querySelectorAll('.catalog-popup').forEach(function(p) { p.style.display = 'none'; });
    }
});

document.addEventListener('input', function(e) {
    if (e.target.classList.contains('amount-input')
        || e.target.id === 'discountInput' || e.target.id === 'vatInput') {
        recalcAll();
    }
});

// ── Catalog picker ──────────────────────────────────────────────────
var catalogDebounce = null;

document.addEventListener('click', function(e) {
    var btn = e.target.closest('.catalog-pick-btn');
    if (!btn) return;
    var td      = btn.closest('td');
    var popup   = td.querySelector('.catalog-popup');
    var descInp = td.querySelector('.desc-input');

    document.querySelectorAll('.catalog-popup').forEach(function(p) {
        if (p !== popup) p.style.display = 'none';
    });

    if (popup.style.display === 'none') {
        popup.innerHTML = '<div style="padding:8px">' +
            '<input type="text" class="form-control" id="catalogSearchInp" placeholder="Search parts…" style="font-size:13px" autocomplete="off">' +
            '</div><div id="catalogResults"></div>';

        var si = popup.querySelector('#catalogSearchInp');
        si.focus();
        si.addEventListener('input', function() {
            clearTimeout(catalogDebounce);
            catalogDebounce = setTimeout(function() { runCatalogSearch(si.value, popup, descInp, btn); }, 280);
        });
        popup.style.display = 'block';
    } else {
        popup.style.display = 'none';
    }
});

function runCatalogSearch(q, popup, descInp, btn) {
    if (!q.trim()) return;
    var res = popup.querySelector('#catalogResults');
    res.innerHTML = '<div style="padding:8px;color:var(--text-muted);font-size:12px">Searching…</div>';
    fetch('/catalog/search?q=' + encodeURIComponent(q))
        .then(function(r) { return r.json(); })
        .then(function(items) {
            if (!items.length) {
                res.innerHTML = '<div style="padding:8px 12px;color:var(--text-muted);font-size:12px">No results found.</div>';
                return;
            }
            res.innerHTML = '';
            items.forEach(function(item) {
                var div = document.createElement('div');
                div.className = 'catalog-popup-item';
                div.innerHTML =
                    '<div class="item-name">' + escHtml(item.name) + '</div>' +
                    '<div class="item-meta">' + escHtml(item.category_name) + ' — ' + CURR + ' ' + parseFloat(item.unit_price).toFixed(2) + '</div>';
                div.addEventListener('click', function() {
                    descInp.value = item.name;
                    var row = btn.closest('tr');
                    var ai  = row.querySelector('.amount-input');
                    if (ai) { ai.value = parseFloat(item.unit_price).toFixed(2); }
                    popup.style.display = 'none';
                    recalcAll();
                });
                res.appendChild(div);
            });
        })
        .catch(function() {
            res.innerHTML = '<div style="padding:8px;color:var(--red);font-size:12px">Search failed.</div>';
        });
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Initial calculation
recalcAll();
</script>
