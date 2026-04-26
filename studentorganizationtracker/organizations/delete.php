<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$id = $_GET['id'];

$conn = getDBConnection();

$stmt = $conn->prepare("DELETE FROM organizations WHERE org_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header('Location: index.php');
exit;
?>
