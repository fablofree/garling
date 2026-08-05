<?php $v = $vehicle ?? []; ?>
<div class="page-header">
    <h1 class="page-title"><?= htmlspecialchars($title ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
    <a href="/vehicles" class="btn btn-outline">← Back</a>
</div>

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
                <input type="text" name="make" class="form-control"
                       value="<?= htmlspecialchars($v['make'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Model</label>
                <input type="text" name="model" class="form-control"
                       value="<?= htmlspecialchars($v['model'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Year</label>
                <input type="number" name="year" class="form-control" min="1950" max="<?= date('Y') + 1 ?>"
                       value="<?= htmlspecialchars((string)($v['year'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <div class="form-grid-3">
            <div class="form-group">
                <label class="form-label">Vehicle Type</label>
                <input type="text" name="vehicle_type" class="form-control"
                       value="<?= htmlspecialchars($v['vehicle_type'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="e.g. Sedan, SUV, Truck">
            </div>
            <div class="form-group">
                <label class="form-label">Colour</label>
                <input type="text" name="colour" class="form-control"
                       value="<?= htmlspecialchars($v['colour'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Distance Unit</label>
                <select name="distance_unit" class="form-control">
                    <option value="km" <?= ($v['distance_unit'] ?? 'km') === 'km' ? 'selected' : '' ?>>Kilometres (km)</option>
                    <option value="miles" <?= ($v['distance_unit'] ?? '') === 'miles' ? 'selected' : '' ?>>Miles</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Servicing Frequency</label>
            <div class="input-with-suffix">
                <input type="number" name="servicing_frequency" class="form-control"
                       value="<?= htmlspecialchars((string)($v['servicing_frequency'] ?? 5000), ENT_QUOTES, 'UTF-8') ?>"
                       min="0">
                <span class="input-suffix">km / miles</span>
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
