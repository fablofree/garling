<?php
// All variables injected by Controller::render() via extract()
// Available: $title, $_app, $_user, $_content, $_flash_success, $_flash_error, $_flash_warning, $_csrf_token
// Also all view-specific variables are available (activeMenu, etc.)

$appName = htmlspecialchars($_app['name'] ?? 'Garage A. Lingiah', ENT_QUOTES, 'UTF-8');
$pageTitle = htmlspecialchars($title ?? 'Dashboard', ENT_QUOTES, 'UTF-8');
$userName  = htmlspecialchars($_user['name'] ?? '', ENT_QUOTES, 'UTF-8');
$flashSuccess = $_flash_success ?? null;
$flashError   = $_flash_error   ?? null;
$flashWarning = $_flash_warning ?? null;
$cfg        = $_app ?? [];
$logoUrl    = htmlspecialchars($cfg['logo_url'] ?? '/assets/images/logo.svg', ENT_QUOTES, 'UTF-8');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> — <?= $appName ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= $logoUrl ?>">
    <meta name="csrf-token" content="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- Main content wrapper -->
    <div class="main-wrapper" id="mainWrapper">
        <!-- Top bar -->
        <header class="topbar">
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            <div class="topbar-title"><?= $pageTitle ?></div>
            <div class="topbar-right">
                <span class="topbar-user">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:6px">
                        <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                    </svg>
                    <?= $userName ?>
                </span>
                <a href="/logout" class="btn-logout">Logout</a>
            </div>
        </header>

        <!-- Flash messages -->
        <div class="flash-container">
            <?php if (!empty($flashSuccess)): ?>
                <div class="alert alert-success">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    <?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?>
                    <button class="alert-close" onclick="this.parentElement.remove()">×</button>
                </div>
            <?php endif; ?>
            <?php if (!empty($flashError)): ?>
                <div class="alert alert-error">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <?= $flashError ?>
                    <button class="alert-close" onclick="this.parentElement.remove()">×</button>
                </div>
            <?php endif; ?>
            <?php if (!empty($flashWarning)): ?>
                <div class="alert alert-warning">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <?= htmlspecialchars($flashWarning, ENT_QUOTES, 'UTF-8') ?>
                    <button class="alert-close" onclick="this.parentElement.remove()">×</button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Page content (rendered view) -->
        <main class="page-content">
            <?= $_content ?? '' ?>
        </main>

        <footer class="page-footer">
            <span><?= $appName ?> &copy; <?= date('Y') ?></span>
        </footer>
    </div>

    <!-- Overlay for mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <script src="/assets/js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
    // Initialize all date inputs with Flatpickr (dd/mm/YYYY display, YYYY-MM-DD value)
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[type="date"]').forEach(function(el) {
            var maxAttr = el.getAttribute('max');
            flatpickr(el, {
                dateFormat: 'Y-m-d',      // value stored as YYYY-MM-DD (PHP reads this)
                altInput: true,
                altFormat: 'd/m/Y',       // display to user as dd/mm/YYYY
                allowInput: true,
                maxDate: maxAttr || null,
            });
        });
    });
    </script>
</body>
</html>
