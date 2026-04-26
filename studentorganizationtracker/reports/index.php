<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">

<h2>Reports</h2>
<p>Select a report below:</p>

<div class="report-cards">

    <div class="card">
        <h3>JOIN Report</h3>
        <p>Shows student participation in organizations and events.</p>
        <a href="students_report.php" class="btn btn-primary">View Report</a>
    </div>

    <div class="card">
        <h3>Subquery Report</h3>
        <p>Lists organizations that have events.</p>
        <a href="organization_report.php" class="btn btn-primary">View Report</a>
    </div>

    <div class="card">
        <h3>CTE Report</h3>
        <p>Displays total attendance per event.</p>
        <a href="attendance_report.php" class="btn btn-primary">View Report</a>
    </div>

</div>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
