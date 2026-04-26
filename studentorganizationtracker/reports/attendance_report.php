<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$conn = getDBConnection();

$query = "
WITH EventAttendance AS (
    SELECT 
        event_id,
        COUNT(student_id) AS total_attendance
    FROM attendance
    GROUP BY event_id
)

SELECT 
    e.event_name,
    ea.total_attendance
FROM EventAttendance ea
JOIN events e ON ea.event_id = e.event_id
";

$result = $conn->query($query);
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
<h2>Event Attendance Report (CTE)</h2>

<table class="data-table">
<tr>
    <th>Event</th>
    <th>Total Attendance</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?php echo htmlspecialchars($row['event_name']); ?></td>
    <td><?php echo htmlspecialchars($row['total_attendance']); ?></td>
</tr>
<?php endwhile; ?>

</table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
