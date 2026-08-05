<?php $ex = $expense ?? []; ?>
<div class="page-header">
    <h1 class="page-title"><?= htmlspecialchars($title ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
    <a href="/expenses" class="btn btn-outline">← Back</a>
</div>

<div class="card" style="max-width:580px">
    <form method="POST" action="<?= htmlspecialchars($action ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label required">Date</label>
                <input type="date" name="expense_date" class="form-control"
                       value="<?= htmlspecialchars($ex['expense_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label required">Category</label>
                <select name="category" class="form-control" required>
                    <option value="">— Select —</option>
                    <?php foreach ($categories ?? [] as $cat): ?>
                    <option value="<?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>"
                        <?= ($ex['category'] ?? '') === $cat ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label required">Description</label>
            <input type="text" name="description" class="form-control"
                   value="<?= htmlspecialchars($ex['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label required">Amount</label>
                <input type="number" name="amount" class="form-control"
                       value="<?= htmlspecialchars((string)($ex['amount'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                       min="0.01" step="0.01" required>
            </div>
            <div class="form-group">
                <label class="form-label">Reference</label>
                <input type="text" name="reference" class="form-control"
                       value="<?= htmlspecialchars($ex['reference'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="2"><?= htmlspecialchars($ex['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= htmlspecialchars($submitLabel ?? 'Save', ENT_QUOTES, 'UTF-8') ?></button>
            <a href="/expenses" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
