<?php
if (($_user['role'] ?? '') !== 'admin') {
    echo '<div class="alert alert-error">Access denied. Admin only.</div>';
    return;
}
$u    = $user ?? [];
$isEdit = !empty($u);
?>
<div class="page-header">
    <h1 class="page-title"><?= htmlspecialchars($title ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
    <a href="/users" class="btn btn-outline">← Back to Users</a>
</div>

<div class="card" style="max-width:560px">
    <form method="POST" action="<?= htmlspecialchars($action ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="form-group" style="padding:20px 20px 0">
            <label class="form-label <?= $isEdit ? '' : 'required' ?>">Username</label>
            <input type="text" name="username" class="form-control"
                   value="<?= htmlspecialchars($u['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   <?= $isEdit ? 'readonly style="background:var(--bg)"' : 'required' ?>>
            <?php if ($isEdit): ?>
                <span class="form-hint">Username cannot be changed.</span>
            <?php endif; ?>
        </div>

        <div class="form-group" style="padding:0 20px">
            <label class="form-label required">Full Name</label>
            <input type="text" name="full_name" class="form-control" required
                   value="<?= htmlspecialchars($u['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-group" style="padding:0 20px">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($u['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-group" style="padding:0 20px">
            <label class="form-label">Role</label>
            <select name="role" class="form-control">
                <option value="staff" <?= ($u['role'] ?? 'staff') === 'staff' ? 'selected' : '' ?>>Staff</option>
                <option value="admin" <?= ($u['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>

        <div class="form-group" style="padding:0 20px">
            <label class="form-label <?= $isEdit ? '' : 'required' ?>">
                Password <?= $isEdit ? '<span class="text-muted font-semibold" style="font-weight:400">(leave blank to keep current)</span>' : '' ?>
            </label>
            <input type="password" name="password" class="form-control" autocomplete="new-password"
                   <?= $isEdit ? '' : 'required' ?>>
        </div>

        <?php if ($isEdit): ?>
        <div class="form-group" style="padding:0 20px">
            <label class="checkbox-label">
                <input type="checkbox" name="is_active" value="1"
                       <?php
                       $ia = $u['is_active'] ?? true;
                       echo ($ia === true || $ia === 't' || $ia === '1' || $ia === 1) ? 'checked' : '';
                       ?>>
                Active account
            </label>
        </div>
        <?php endif; ?>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg"><?= htmlspecialchars($submitLabel ?? 'Save', ENT_QUOTES, 'UTF-8') ?></button>
            <a href="/users" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
