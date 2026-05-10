<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<!-- Font Awesome (REAL icons) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="page-header">
    <div>
        <h2>Reports & Analytics</h2>
        <p>Visualize and explore your system data.</p>
    </div>
</div>

<!-- 🔥 SUMMARY CARDS -->
<div class="summary-cards">

    <div class="summary-card">
        <i class="fas fa-users"></i>
        <h3>120</h3>
        <p>Total Students</p>
    </div>

    <div class="summary-card">
        <i class="fas fa-building"></i>
        <h3>10</h3>
        <p>Organizations</p>
    </div>

    <div class="summary-card">
        <i class="fas fa-calendar"></i>
        <h3>15</h3>
        <p>Events</p>
    </div>

    <div class="summary-card">
        <i class="fas fa-chart-line"></i>
        <h3>300</h3>
        <p>Attendance</p>
    </div>

</div>

<!-- 🔥 CHART -->
<div class="card card-padded">
    <h4 style="margin-bottom:15px;">Attendance Overview</h4>
    <canvas id="attendanceChart"></canvas>
</div>

<script>
const ctx = document.getElementById('attendanceChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Event 1', 'Event 2', 'Event 3'],
        datasets: [{
            label: 'Attendance',
            data: [12, 19, 7],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true
    }
});
</script>

<!-- 🔥 REPORT CARDS -->
<div class="action-grid">

    <div class="action-card">
        <div class="report-icon-wrap">
            <i class="fas fa-users"></i>
        </div>
        <h4>JOIN Report</h4>
        <p>View student participation across organizations and events.</p>
        <a href="students_report.php" class="btn btn-primary btn-sm">
            View Report <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <div class="action-card">
        <div class="report-icon-wrap">
            <i class="fas fa-search"></i>
        </div>
        <h4>Subquery Report</h4>
        <p>Organizations that currently have at least one event.</p>
        <a href="organization_report.php" class="btn btn-primary btn-sm">
            View Report <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <div class="action-card">
        <div class="report-icon-wrap">
            <i class="fas fa-chart-bar"></i>
        </div>
        <h4>CTE Report</h4>
        <p>Total attendance records per event.</p>
        <a href="attendance_report.php" class="btn btn-primary btn-sm">
            View Report <i class="fas fa-arrow-right"></i>
        </a>
    </div>

</div>

<!-- INFO CARD -->
<div class="card card-padded" style="max-width:600px; margin-top:20px;">
    <h4>About These Reports</h4>
    <p style="font-size:14px; color:var(--secondary); line-height:1.6;">
        Each report demonstrates SQL techniques: JOINs combine tables,
        Subqueries filter results, and CTEs simplify complex queries.
    </p>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
