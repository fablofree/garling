<?php
if (($_user['role'] ?? '') !== 'admin') {
    echo '<div class="alert alert-error">Access denied. Admin only.</div>';
    return;
}
?>
<div class="page-header">
    <h1 class="page-title">User Management</h1>
    <a href="/users/create" class="btn btn-primary">+ New User</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th style="width:140px">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users ?? [] as $u): ?>
            <tr>
                <td class="font-semibold"><?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($u['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td class="text-muted"><?= htmlspecialchars($u['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <?php if ($u['role'] === 'admin'): ?>
                        <span class="badge badge-invoice">Admin</span>
                    <?php else: ?>
                        <span class="badge badge-category">Staff</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($u['is_active'] === true || $u['is_active'] === 't' || $u['is_active'] === '1' || $u['is_active'] === 1): ?>
                        <span class="badge badge-paid">Active</span>
                    <?php else: ?>
                        <span class="badge badge-unpaid">Inactive</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="action-group">
                        <a href="/users/<?= (int)$u['id'] ?>/edit" class="btn btn-xs btn-outline">Edit</a>
                        <?php if ((int)$u['id'] !== (int)($_user['id'] ?? 0)): ?>
                        <form method="POST" action="/users/<?= (int)$u['id'] ?>/delete"
                              onsubmit="return confirm('Delete user <?= htmlspecialchars(addslashes($u['username']), ENT_QUOTES, 'UTF-8') ?>?')" style="display:inline">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                        </form>
                        <?php else: ?>
                            <span class="text-muted" style="font-size:11px">(you)</span>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($users)): ?>
            <tr><td colspan="6" class="text-center text-muted py-8">No users found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
