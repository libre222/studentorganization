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

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="container">

    <!-- 🔥 HEADER + PRINT BUTTON -->
    <div class="report-header">
        <div>
            <h2>Organization Report</h2>
            <p>Organizations with at least one event</p>
            <small>Date: <?php echo date("F d, Y"); ?></small>
        </div>

        <button onclick="printReport()" class="btn-print">
            <i class="fas fa-print"></i> Print
        </button>
    </div>

    <!-- 🔥 PRINT AREA -->
    <div class="print-area">

        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Organization Name</th>
                </tr>
            </thead>

            <tbody>
                <?php $count = 1; ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $count++; ?></td>
                    <td><?php echo htmlspecialchars($row['org_name']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>

</div>

<script>
function printReport() {
    window.print();
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
