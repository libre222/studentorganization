<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$conn = getDBConnection();

$query = "
SELECT 
    s.student_name,
    o.org_name,
    e.event_name
FROM students s
JOIN memberships m ON s.student_id = m.student_id
JOIN organizations o ON m.org_id = o.org_id
JOIN events e ON o.org_id = e.org_id
";

$result = $conn->query($query);
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
<h2>Students Participation Report (JOIN)</h2>

<table class="data-table">
<tr>
    <th>Student</th>
    <th>Organization</th>
    <th>Event</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
    <td><?php echo htmlspecialchars($row['org_name']); ?></td>
    <td><?php echo htmlspecialchars($row['event_name']); ?></td>
</tr>
<?php endwhile; ?>

</table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
