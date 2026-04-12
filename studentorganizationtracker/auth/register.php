<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Please fill all fields.';
    } elseif (strlen($password) < 6) {
        $_SESSION['error'] = 'Password must be at least 6 characters.';
    } else {
        $conn = getDBConnection();
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed_password);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Registration successful! Please login.';
            header('Location: login.php');
            exit;
        } else {
            $_SESSION['error'] = 'Username already exists.';
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Student Org Tracker</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        
        <p>Already have account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>

