<?php
$e      = $entry ?? [];
$cfg    = $_app ?? [];
$cur    = $cfg['currency']['symbol'] ?? 'Rs';
$fmt    = fn(float $v) => $cur . ' ' . number_format($v, 2);
$total  = (float)($e['total_cost'] ?? 0);
$invNo  = 'INV-' . str_pad((string)($e['id'] ?? ''), 6, '0', STR_PAD_LEFT);
?>
<div class="page-header">
    <h1 class="page-title">Record Payment</h1>
    <a href="/services/<?= (int)($e['id'] ?? 0) ?>" class="btn btn-outline">← Back to Entry</a>
</div>

<!-- Summary -->
<div class="stats-grid stats-grid-3 mb-6">
    <div class="stat-card stat-card-sm">
        <div class="stat-body">
            <div class="stat-label">Invoice</div>
            <div class="stat-value" style="font-size:1.1rem"><?= htmlspecialchars($invNo, ENT_QUOTES, 'UTF-8') ?></div>
        </div>
    </div>
    <div class="stat-card stat-card-sm">
        <div class="stat-body">
            <div class="stat-label">Invoice Total</div>
            <div class="stat-value"><?= $fmt($total) ?></div>
        </div>
    </div>
    <div class="stat-card stat-card-sm">
        <div class="stat-body">
            <div class="stat-label">Outstanding Balance</div>
            <div class="stat-value text-red"><?= $fmt((float)($balance ?? 0)) ?></div>
        </div>
    </div>
</div>

<div class="card" style="max-width:580px">
    <div class="card-header">
        <h2 class="card-title">Payment Details</h2>
    </div>
    <form method="POST" action="<?= htmlspecialchars($action ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label required">Payment Date</label>
                <input type="date" name="payment_date" class="form-control"
                       value="<?= date('Y-m-d') ?>"
                       max="<?= date('Y-m-d') ?>"
                       required>
            </div>
            <div class="form-group">
                <label class="form-label required">Payment Method</label>
                <select name="payment_method" class="form-control" id="paymentMethod">
                    <option value="CASH">Cash</option>
                    <option value="CHEQUE">Cheque</option>
                    <option value="CARD">Card</option>
                    <option value="TRANSFER">Bank Transfer</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label required">Amount</label>
            <div class="input-with-prefix">
                <span class="input-prefix"><?= htmlspecialchars($cur, ENT_QUOTES, 'UTF-8') ?></span>
                <input type="number" name="amount" class="form-control" id="amountInput"
                       value="<?= number_format((float)($balance ?? 0), 2, '.', '') ?>"
                       min="0.01" step="0.01" required>
            </div>
            <small class="form-hint">Balance outstanding: <?= $fmt((float)($balance ?? 0)) ?></small>
        </div>

        <div class="form-group" id="chequeGroup" style="display:none">
            <label class="form-label">Cheque Number</label>
            <input type="text" name="cheque_number" class="form-control">
        </div>

        <div class="form-group">
            <label class="form-label">Reference</label>
            <input type="text" name="reference" class="form-control" placeholder="Receipt/ref number...">
        </div>

        <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">Record Payment</button>
            <a href="/services/<?= (int)($e['id'] ?? 0) ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<script>
document.getElementById('paymentMethod').addEventListener('change', function() {
    document.getElementById('chequeGroup').style.display = this.value === 'CHEQUE' ? 'block' : 'none';
});
</script>
