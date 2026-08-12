<?php
if (($_user['role'] ?? '') !== 'admin') {
    echo '<div class="alert alert-error">Access denied. Admin only.</div>';
    return;
}
$s = $settings ?? [];
?>
<div class="page-header">
    <h1 class="page-title">App Settings</h1>
</div>

<!-- General settings -->
<div class="card mb-4" style="max-width:700px">
    <div class="card-header"><h2 class="card-title">General Settings</h2></div>
    <form method="POST" action="/settings/update">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="form-grid-2" style="padding:20px 20px 0">
            <div class="form-group">
                <label class="form-label">App Name</label>
                <input type="text" name="app_name" class="form-control"
                       value="<?= htmlspecialchars($s['app_name'] ?? 'Garage A. Lingiah', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Currency Symbol</label>
                <input type="text" name="currency_symbol" class="form-control"
                       value="<?= htmlspecialchars($s['currency_symbol'] ?? 'Rs', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <div class="form-grid-2" style="padding:0 20px">
            <div class="form-group">
                <label class="form-label">BRN (Business Registration No.)</label>
                <input type="text" name="app_brn" class="form-control"
                       value="<?= htmlspecialchars($s['app_brn'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">VAT Registration No.</label>
                <input type="text" name="app_vat_reg" class="form-control"
                       value="<?= htmlspecialchars($s['app_vat_reg'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <div class="form-group" style="padding:0 20px">
            <label class="form-label">Address</label>
            <textarea name="app_address" class="form-control" rows="2"><?= htmlspecialchars($s['app_address'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="form-grid-2" style="padding:0 20px">
            <div class="form-group">
                <label class="form-label">Telephone</label>
                <input type="text" name="app_tel" class="form-control"
                       value="<?= htmlspecialchars($s['app_tel'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="app_email" class="form-control"
                       value="<?= htmlspecialchars($s['app_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <div class="form-group" style="padding:0 20px">
            <label class="form-label">Default VAT %</label>
            <input type="number" name="vat_default" class="form-control" style="max-width:120px"
                   min="0" max="100" step="0.01"
                   value="<?= htmlspecialchars($s['vat_default'] ?? '0', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>
</div>

<!-- Logo upload -->
<div class="card" style="max-width:700px">
    <div class="card-header"><h2 class="card-title">App Logo</h2></div>
    <div style="padding:20px">
        <div style="margin-bottom:16px">
            <p class="form-label" style="margin-bottom:8px">Current Logo</p>
            <?php
            $logoUrl = !empty($s['app_logo'])
                ? '/' . ltrim($s['app_logo'], '/')
                : '/assets/images/logo.svg';
            ?>
            <img src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>"
                 alt="Current logo"
                 style="height:80px;border:1px solid var(--border);border-radius:var(--radius);padding:8px;background:#1e293b">
        </div>

        <form method="POST" action="/settings/logo" enctype="multipart/form-data">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <div class="form-group">
                <label class="form-label">Upload New Logo</label>
                <input type="file" name="logo" class="form-control" accept=".png,.jpg,.jpeg,.svg">
                <span class="form-hint">PNG, JPG, or SVG — max 2 MB. Recommended: at least 80×80px.</span>
            </div>
            <button type="submit" class="btn btn-primary">Upload Logo</button>
        </form>
    </div>
</div>
