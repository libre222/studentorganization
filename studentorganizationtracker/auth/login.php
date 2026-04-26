<?php
session_start();

// If already logged in → go to dashboard
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
    <title>Login - Student Org Tracker</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container" style="display:flex; justify-content:center; align-items:center; height:100vh;">

    <div class="card" style="width:350px;">

        <h2 style="text-align:center; margin-bottom:20px;">
            Student Org Tracker
        </h2>

        <!-- ERROR MESSAGE -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- SUCCESS MESSAGE -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success">
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- LOGIN FORM -->
        <form method="POST" action="login_process.php">

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; margin-top:10px;">
                Login
            </button>

        </form>

        <p style="text-align:center; margin-top:15px; font-size:13px;">
            Don't have an account? 
            <a href="register.php">Register</a>
        </p>

    </div>

</div>

</body>
</html>
