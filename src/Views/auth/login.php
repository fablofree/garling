<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Garage A. Lingiah</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; background: #0f172a; display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: 'Inter', sans-serif; }
        .login-bg {
            position: fixed; inset: 0; z-index: 0;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100'%3E%3Ccircle cx='50' cy='50' r='40' fill='none' stroke='%23f9731620' stroke-width='1'/%3E%3C/svg%3E") repeat;
            background-color: #0f172a;
        }
        .login-wrapper { position: relative; z-index: 1; width: 100%; max-width: 420px; padding: 20px; }
        .login-logo {
            text-align: center; margin-bottom: 32px;
        }
        .login-logo svg { width: 56px; height: 56px; }
        .login-logo h1 { color: #f97316; font-size: 24px; font-weight: 700; margin: 12px 0 4px; }
        .login-logo p { color: #94a3b8; font-size: 13px; margin: 0; }
        .login-card {
            background: #1e293b; border-radius: 16px; padding: 36px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            border: 1px solid #334155;
        }
        .login-card h2 { color: #f1f5f9; font-size: 18px; font-weight: 600; margin: 0 0 24px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: #94a3b8; font-size: 13px; font-weight: 500; margin-bottom: 8px; }
        .form-group input {
            width: 100%; box-sizing: border-box;
            background: #0f172a; border: 1px solid #334155; border-radius: 8px;
            color: #f1f5f9; font-size: 15px; padding: 12px 16px;
            outline: none; transition: border-color 0.2s;
            font-family: 'Inter', sans-serif;
        }
        .form-group input:focus { border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,0.15); }
        .login-btn {
            width: 100%; padding: 13px; background: #f97316; color: #fff;
            border: none; border-radius: 8px; font-size: 15px; font-weight: 600;
            cursor: pointer; transition: background 0.2s; font-family: 'Inter', sans-serif;
        }
        .login-btn:hover { background: #ea6c0a; }
        .alert-error {
            background: #450a0a; border: 1px solid #dc2626; color: #fca5a5;
            border-radius: 8px; padding: 12px 16px; margin-bottom: 20px;
            font-size: 14px;
        }
        .login-footer { text-align: center; margin-top: 20px; color: #475569; font-size: 13px; }
    </style>
</head>
<body>
    <div class="login-bg"></div>
    <div class="login-wrapper">
        <div class="login-logo">
            <svg viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="1.5">
                <rect x="1" y="3" width="15" height="13" rx="2"/>
                <path d="M16 8h4l3 6v3h-7V8z"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
            <h1>Garage A. Lingiah</h1>
            <p>Garage Management System</p>
        </div>

        <div class="login-card">
            <h2>Sign In</h2>

            <?php if (!empty($_flash_error)): ?>
                <div class="alert-error"><?= htmlspecialchars($_flash_error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form method="POST" action="/login">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" autocomplete="username"
                           placeholder="Enter your username" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" autocomplete="current-password"
                           placeholder="Enter your password" required>
                </div>

                <button type="submit" class="login-btn">Sign In</button>
            </form>
        </div>

        <div class="login-footer">
            Garage Management System v1.0
        </div>
    </div>
</body>
</html>
