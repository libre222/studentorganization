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

// 🔥 PREPARE DATA FOR CHART
$events = [];
$attendance = [];
$total = 0;

while($row = $result->fetch_assoc()){
    $events[] = $row['event_name'];
    $attendance[] = $row['total_attendance'];
    $total += $row['total_attendance'];
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container">

    <!-- 🔥 HEADER -->
    <div class="report-header">
        <div>
            <h2>Attendance Report</h2>
            <p>Event attendance summary (CTE)</p>
            <small>Date: <?php echo date("F d, Y"); ?></small>
        </div>

        <button onclick="printReport()" class="btn-print">
            <i class="fas fa-print"></i> Print
        </button>
    </div>

    <!-- 🔥 SUMMARY -->
    <div class="summary-card" style="margin-bottom:20px;">
        <h3><?php echo $total; ?></h3>
        <p>Total Attendance</p>
    </div>

    <!-- 🔥 CHART -->
    <div class="card card-padded" style="margin-bottom:20px;">
        <h4>Attendance per Event</h4>
        <canvas id="attendanceChart"></canvas>
    </div>

    <!-- 🔥 PRINT AREA -->
    <div class="print-area">

        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Event</th>
                    <th>Total Attendance</th>
                </tr>
            </thead>

            <tbody>
                <?php 
                $count = 1;
                foreach ($events as $index => $event): 
                ?>
                <tr>
                    <td><?php echo $count++; ?></td>
                    <td><?php echo htmlspecialchars($event); ?></td>
                    <td><?php echo htmlspecialchars($attendance[$index]); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>

        </table>

    </div>

</div>

<script>
// 🔥 CHART.JS
const ctx = document.getElementById('attendanceChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($events); ?>,
        datasets: [{
            label: 'Attendance',
            data: <?php echo json_encode($attendance); ?>,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true
    }
});

// PRINT
function printReport() {
    window.print();
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
