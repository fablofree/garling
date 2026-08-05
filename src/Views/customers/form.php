<?php $c = $customer ?? []; ?>
<div class="page-header">
    <h1 class="page-title"><?= htmlspecialchars($title ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
    <a href="/customers" class="btn btn-outline">← Back</a>
</div>

<div class="card" style="max-width:700px">
    <form method="POST" action="<?= htmlspecialchars($action ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label required">Full Name</label>
                <input type="text" name="name" class="form-control"
                       value="<?= htmlspecialchars($c['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($c['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($c['address'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="form-grid-3">
            <div class="form-group">
                <label class="form-label">Tel. Home</label>
                <input type="tel" name="tel_home" class="form-control"
                       value="<?= htmlspecialchars($c['tel_home'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Tel. Office</label>
                <input type="tel" name="tel_office" class="form-control"
                       value="<?= htmlspecialchars($c['tel_office'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Mobile</label>
                <input type="tel" name="tel_mobile" class="form-control"
                       value="<?= htmlspecialchars($c['tel_mobile'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Fax</label>
                <input type="tel" name="fax" class="form-control"
                       value="<?= htmlspecialchars($c['fax'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="2"><?= htmlspecialchars($c['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= htmlspecialchars($submitLabel ?? 'Save', ENT_QUOTES, 'UTF-8') ?></button>
            <a href="/customers" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
