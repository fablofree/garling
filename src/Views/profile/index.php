<?php $u = $user ?? []; ?>
<div class="page-header">
    <h1 class="page-title">My Profile</h1>
</div>

<!-- Personal info -->
<div class="card mb-4" style="max-width:520px">
    <div class="card-header"><h2 class="card-title">Personal Information</h2></div>
    <form method="POST" action="/profile/update">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <!-- hidden password fields to satisfy browser autofill -->
        <input type="hidden" name="current_password" value="">
        <input type="hidden" name="new_password" value="">
        <input type="hidden" name="confirm_password" value="">

        <div class="form-group" style="padding:20px 20px 0">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($u['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
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
            <input type="text" class="form-control" value="<?= htmlspecialchars(ucfirst($u['role'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                   readonly style="background:var(--bg)">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</div>

<!-- Password change -->
<div class="card" style="max-width:520px">
    <div class="card-header"><h2 class="card-title">Change Password</h2></div>
    <form method="POST" action="/profile/update" autocomplete="off">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="full_name" value="<?= htmlspecialchars($u['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="email" value="<?= htmlspecialchars($u['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="form-group" style="padding:20px 20px 0">
            <label class="form-label">Current Password</label>
            <input type="password" name="current_password" class="form-control" autocomplete="current-password">
        </div>

        <div class="form-group" style="padding:0 20px">
            <label class="form-label">New Password</label>
            <input type="password" name="new_password" class="form-control" autocomplete="new-password"
                   minlength="6">
            <span class="form-hint">Minimum 6 characters.</span>
        </div>

        <div class="form-group" style="padding:0 20px">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control" autocomplete="new-password">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Password</button>
        </div>
    </form>
</div>
