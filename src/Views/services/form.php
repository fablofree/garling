<?php
$e          = $entry ?? [];
$isEdit     = !empty($e);
$selVehicle = (int)($e['vehicle_id'] ?? $preselect_vehicle ?? 0);
$selCust    = (int)($e['customer_id'] ?? $preselect_customer ?? 0);
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
        <div class="form-grid-3">
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
            <div class="form-group">
                <label class="form-label">Type</label>
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_quotation" value="1"
                               <?= !empty($e['is_quotation']) && $e['is_quotation'] !== 'f' ? 'checked' : '' ?>
                               id="isQuotation">
                        Quotation (not Invoice)
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_completed" value="1"
                               <?= !empty($e['is_completed']) && $e['is_completed'] !== 'f' ? 'checked' : '' ?>>
                        Completed
                    </label>
                </div>
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
                <input type="number" name="odometer" class="form-control" min="0"
                       value="<?= htmlspecialchars((string)($e['odometer'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Next Servicing At</label>
                <input type="number" name="next_servicing" class="form-control" min="0"
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
                        <th style="width:50%">Description</th>
                        <th style="width:15%">Qty</th>
                        <th style="width:20%">Unit Price</th>
                        <th style="width:15%">Total</th>
                        <th style="width:40px"></th>
                    </tr>
                </thead>
                <tbody id="partsBody">
                <?php
                $partsData = $spare_parts ?? [];
                if (empty($partsData)) {
                    $partsData = [['description' => '', 'quantity' => 1, 'unit_price' => '', 'total_price' => '']];
                }
                foreach ($partsData as $p):
                ?>
                <tr class="line-item-row">
                    <td><input type="text" name="parts_description[]" class="form-control" value="<?= htmlspecialchars($p['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Description"></td>
                    <td><input type="number" name="parts_quantity[]" class="form-control qty-input" value="<?= htmlspecialchars((string)($p['quantity'] ?? 1), ENT_QUOTES, 'UTF-8') ?>" min="0" step="0.01"></td>
                    <td><input type="number" name="parts_unit_price[]" class="form-control price-input" value="<?= htmlspecialchars((string)($p['unit_price'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" min="0" step="0.01" placeholder="0.00"></td>
                    <td><input type="text" class="form-control line-total" value="<?= htmlspecialchars(number_format((float)($p['total_price'] ?? 0), 2), ENT_QUOTES, 'UTF-8') ?>" readonly tabindex="-1"></td>
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
                        <th style="width:50%">Description</th>
                        <th style="width:15%">Qty</th>
                        <th style="width:20%">Unit Price</th>
                        <th style="width:15%">Total</th>
                        <th style="width:40px"></th>
                    </tr>
                </thead>
                <tbody id="repairsBody">
                <?php
                $repairsData = $repairs ?? [];
                if (empty($repairsData)) {
                    $repairsData = [['description' => '', 'quantity' => 1, 'unit_price' => '', 'total_price' => '']];
                }
                foreach ($repairsData as $r):
                ?>
                <tr class="line-item-row">
                    <td><input type="text" name="repairs_description[]" class="form-control" value="<?= htmlspecialchars($r['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Description"></td>
                    <td><input type="number" name="repairs_quantity[]" class="form-control qty-input" value="<?= htmlspecialchars((string)($r['quantity'] ?? 1), ENT_QUOTES, 'UTF-8') ?>" min="0" step="0.01"></td>
                    <td><input type="number" name="repairs_unit_price[]" class="form-control price-input" value="<?= htmlspecialchars((string)($r['unit_price'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" min="0" step="0.01" placeholder="0.00"></td>
                    <td><input type="text" class="form-control line-total" value="<?= htmlspecialchars(number_format((float)($r['total_price'] ?? 0), 2), ENT_QUOTES, 'UTF-8') ?>" readonly tabindex="-1"></td>
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
                    <label class="form-label">Discount (Rs)</label>
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

            <div class="totals-summary">
                <div class="total-row"><span>Parts Total</span><span id="summaryParts">Rs 0.00</span></div>
                <div class="total-row"><span>Labour Total</span><span id="summaryLabour">Rs 0.00</span></div>
                <div class="total-row"><span>Discount</span><span id="summaryDiscount">Rs 0.00</span></div>
                <div class="total-row"><span>Subtotal</span><span id="summarySubtotal">Rs 0.00</span></div>
                <div class="total-row"><span>VAT</span><span id="summaryVat">Rs 0.00</span></div>
                <div class="total-row total-row-grand"><span>TOTAL</span><span id="summaryTotal">Rs 0.00</span></div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary btn-lg"><?= htmlspecialchars($submitLabel ?? 'Save', ENT_QUOTES, 'UTF-8') ?></button>
        <a href="/services" class="btn btn-outline">Cancel</a>
    </div>
</form>

<script>
// Vehicle/Customer sync
document.getElementById('vehicleSelect').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const custId = opt.getAttribute('data-customer-id');
    if (custId) {
        document.getElementById('customerSelect').value = custId;
    }
});
document.getElementById('customerSelect').addEventListener('change', function() {
    const custId = this.value;
    const vs = document.getElementById('vehicleSelect');
    // Reset vehicle selection if customer changes
    for (let opt of vs.options) {
        if (opt.getAttribute('data-customer-id') !== custId) {
            opt.style.display = 'none';
        } else {
            opt.style.display = '';
        }
    }
    vs.value = '';
});

// Line items calculation
function calcRow(row) {
    const qty   = parseFloat(row.querySelector('.qty-input')?.value) || 0;
    const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
    const total = qty * price;
    const totalEl = row.querySelector('.line-total');
    if (totalEl) totalEl.value = total.toFixed(2);
    return total;
}

function recalcAll() {
    let partsTotal  = 0;
    let labourTotal = 0;

    document.querySelectorAll('#partsBody .line-item-row').forEach(r => { partsTotal += calcRow(r); });
    document.querySelectorAll('#repairsBody .line-item-row').forEach(r => { labourTotal += calcRow(r); });

    const discount  = parseFloat(document.getElementById('discountInput')?.value) || 0;
    const vatPct    = parseFloat(document.getElementById('vatInput')?.value) || 0;
    const subtotal  = Math.max(0, partsTotal + labourTotal - discount);
    const vatAmount = subtotal * vatPct / 100;
    const total     = subtotal + vatAmount;

    document.getElementById('partsTotalDisplay').textContent  = partsTotal.toFixed(2);
    document.getElementById('repairsTotalDisplay').textContent = labourTotal.toFixed(2);
    document.getElementById('summaryParts').textContent    = 'Rs ' + partsTotal.toFixed(2);
    document.getElementById('summaryLabour').textContent   = 'Rs ' + labourTotal.toFixed(2);
    document.getElementById('summaryDiscount').textContent = 'Rs ' + discount.toFixed(2);
    document.getElementById('summarySubtotal').textContent = 'Rs ' + subtotal.toFixed(2);
    document.getElementById('summaryVat').textContent      = 'Rs ' + vatAmount.toFixed(2);
    document.getElementById('summaryTotal').textContent    = 'Rs ' + total.toFixed(2);
}

function makeRow(prefix) {
    const tr = document.createElement('tr');
    tr.className = 'line-item-row';
    tr.innerHTML = `
        <td><input type="text" name="${prefix}_description[]" class="form-control" placeholder="Description"></td>
        <td><input type="number" name="${prefix}_quantity[]" class="form-control qty-input" value="1" min="0" step="0.01"></td>
        <td><input type="number" name="${prefix}_unit_price[]" class="form-control price-input" min="0" step="0.01" placeholder="0.00"></td>
        <td><input type="text" class="form-control line-total" value="0.00" readonly tabindex="-1"></td>
        <td><button type="button" class="btn btn-xs btn-danger remove-row">×</button></td>
    `;
    return tr;
}

document.getElementById('addPartBtn').addEventListener('click', () => {
    document.getElementById('partsBody').appendChild(makeRow('parts'));
});
document.getElementById('addRepairBtn').addEventListener('click', () => {
    document.getElementById('repairsBody').appendChild(makeRow('repairs'));
});

document.addEventListener('click', e => {
    if (e.target.classList.contains('remove-row')) {
        const row  = e.target.closest('tr');
        const body = row.parentElement;
        if (body.querySelectorAll('tr').length > 1) {
            row.remove();
            recalcAll();
        } else {
            // Clear the row instead of removing
            row.querySelectorAll('input').forEach(inp => { if (!inp.readOnly) inp.value = ''; });
            row.querySelector('.line-total').value = '0.00';
            recalcAll();
        }
    }
});

document.addEventListener('input', e => {
    if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')
        || e.target.id === 'discountInput' || e.target.id === 'vatInput') {
        recalcAll();
    }
});

// Initial calculation
recalcAll();
</script>
