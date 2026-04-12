<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Please fill all fields.';
        header('Location: login.php');
        exit;
    }
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['success'] = 'Login successful!';
            header('Location: ../students/index.php');
            exit;
        } else {
            $_SESSION['error'] = 'Invalid password.';
        }
    } else {
        $_SESSION['error'] = 'User not found.';
    }
    
    $stmt->close();
    $conn->close();
    header('Location: login.php');
    exit;
}
?>

