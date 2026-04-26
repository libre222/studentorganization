<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /studentorganizationtracker/auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Org Tracker</title>
<link rel="stylesheet" href="/studentorganizationtracker/assets/css/style.css">
</head>
<body>

<div class="layout">
