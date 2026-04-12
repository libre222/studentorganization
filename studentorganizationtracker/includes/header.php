<?php
session_start();
if (basename($_SERVER['PHP_SELF']) !== 'login.php' && 
    basename($_SERVER['PHP_SELF']) !== 'register.php' && 
    basename($_SERVER['PHP_SELF']) !== 'login_process.php' && 
    !isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Student Organization Tracker</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

