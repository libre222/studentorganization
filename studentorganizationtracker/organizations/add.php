<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['org_name'];
    $desc = $_POST['description'];
    $date = $_POST['date_created'];

    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO organizations (org_name, description, date_created) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $desc, $date);

    if ($stmt->execute()) {
        header('Location: index.php');
        exit;
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container">
    <h2>Add Organization</h2>

    <form method="POST">
        <input type="text" name="org_name" placeholder="Organization Name" required><br><br>
        <textarea name="description" placeholder="Description"></textarea><br><br>
        <input type="date" name="date_created" required><br><br>

        <button type="submit">Add</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
