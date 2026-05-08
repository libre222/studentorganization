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
<title>Sign In — Student Org Tracker</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="auth-page">
  <div class="auth-card">

    <div class="auth-logo">
      <div class="logo-mark">
        <span class="material-symbols-rounded">corporate_fare</span>
      </div>
      <h1>Student Org Tracker</h1>
      <p>Sign in to your administrator account</p>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert error">
        <span class="material-symbols-rounded">error</span>
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
      <div class="alert success">
        <span class="material-symbols-rounded">check_circle</span>
        <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="login_process.php">

      <div class="form-group" style="margin-bottom:16px;">
        <label>Username</label>
        <input type="text" name="username" placeholder="Enter your username" required autofocus>
      </div>

      <div class="form-group" style="margin-bottom:24px;">
        <label>Password</label>
        <input type="password" name="password" placeholder="••••••••" required>
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px 20px;font-size:14px;">
        Sign In
        <span class="material-symbols-rounded">arrow_forward</span>
      </button>

    </form>

    <p style="text-align:center;margin-top:20px;font-size:13px;color:var(--text-muted);">
      Don't have an account?
      <a href="register.php" style="color:var(--indigo-500);font-weight:600;">Register</a>
    </p>

  </div>
</div>

</body>
</html>
