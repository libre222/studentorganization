<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$conn = getDBConnection();

// 🔥 JOIN QUERY (adjust if needed)
$query = "
SELECT 
    s.student_name,
    o.org_name,
    e.event_name
FROM students s
JOIN memberships m ON s.student_id = m.student_id
JOIN organizations o ON m.org_id = o.org_id
LEFT JOIN attendance a ON s.student_id = a.student_id
LEFT JOIN events e ON a.event_id = e.event_id
ORDER BY s.student_name
";

$result = $conn->query($query);

// prepare for chart
$orgs = [];
$counts = [];

while ($row = $result->fetch_assoc()) {
    $org = $row['org_name'];
    if (!isset($counts[$org])) {
        $counts[$org] = 0;
    }
    $counts[$org]++;
    $data[] = $row;
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container">

    <!-- HEADER -->
    <div class="report-header">
        <div>
            <h2>Student Participation Report</h2>
            <p>Student involvement in organizations and events (JOIN)</p>
            <small>Date: <?php echo date("F d, Y"); ?></small>
        </div>

        <button onclick="printReport()" class="btn-print">
            <i class="fas fa-print"></i> Print
        </button>
    </div>

    <!-- 🔥 CHART -->
    <div class="card card-padded" style="margin-bottom:20px;">
        <h4>Students per Organization</h4>
        <canvas id="joinChart"></canvas>
    </div>

    <!-- 🔥 PRINT AREA -->
    <div class="print-area">

        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Organization</th>
                    <th>Event</th>
                </tr>
            </thead>

            <tbody>
                <?php $count = 1; ?>
                <?php foreach($data as $row): ?>
                <tr>
                    <td><?php echo $count++; ?></td>
                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['org_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['event_name'] ?? 'N/A'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>

        </table>

    </div>

</div>

<script>
// chart
const ctx = document.getElementById('joinChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_keys($counts)); ?>,
        datasets: [{
            label: 'Students',
            data: <?php echo json_encode(array_values($counts)); ?>,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true
    }
});

// print
function printReport() {
    window.print();
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
