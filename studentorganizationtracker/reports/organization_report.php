<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$conn = getDBConnection();

$query = "
SELECT org_name
FROM organizations
WHERE org_id IN (
    SELECT org_id FROM events
)
";

$result = $conn->query($query);
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
<h2>Organizations with Events (SUBQUERY)</h2>

<table class="data-table">
<tr>
    <th>Organization Name</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?php echo htmlspecialchars($row['org_name']); ?></td>
</tr>
<?php endwhile; ?>

</table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
