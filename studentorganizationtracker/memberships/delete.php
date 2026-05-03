<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$id = $_GET['id'] ?? 0;

if (!$id) {
    $_SESSION['error'] = 'Invalid membership ID.';
    header('Location: index.php');
    exit;
}

$conn = getDBConnection();
$stmt = $conn->prepare("DELETE FROM memberships WHERE membership_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Membership removed successfully!';
} else {
    $_SESSION['error'] = 'Failed to remove membership.';
}

$stmt->close();
$conn->close();

header('Location: index.php');
exit;
?>
