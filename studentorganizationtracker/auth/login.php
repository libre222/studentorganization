<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Student Org Tracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="auth-page">
    <div class="auth-card">

        <!-- Logo -->
        <div class="auth-logo">
            <div class="logo-icon">🎓</div>
            <h1>Student Org Tracker</h1>
            <p>Sign in to your admin account</p>
        </div>

        <!-- Alerts -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error">
                ⚠️ <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success">
                ✅ <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="login_process.php">

            <div class="form-group" style="margin-bottom:14px;">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter your username" required autofocus>
            </div>

            <div class="form-group" style="margin-bottom:20px;">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding:12px;">
                Sign In →
            </button>

        </form>

        <p style="text-align:center; margin-top:20px; font-size:13px; color:var(--text-muted);">
            Don't have an account?
            <a href="register.php" style="color:var(--accent); font-weight:600;">Register</a>
        </p>

    </div>
</div>

</body>
</html>
