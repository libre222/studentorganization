<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$id = $_GET['id'] ?? 0;
if (!$id) {
    $_SESSION['error'] = 'Invalid ID.';
    header('Location: index.php');
    exit;
}

$conn = getDBConnection();
$stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $_SESSION['success'] = 'Student deleted successfully!';
    } else {
        $_SESSION['error'] = 'Student not found.';
    }
} else {
    $_SESSION['error'] = 'Delete failed.';
}

$stmt->close();
$conn->close();
header('Location: index.php');
exit;
?>

