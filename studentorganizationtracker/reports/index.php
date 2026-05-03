<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="page-header">
    <div>
        <h2>Reports &amp; Analytics</h2>
        <p>Explore data using JOINs, Subqueries, and CTEs.</p>
    </div>
</div>

<div class="action-grid">

    <div class="action-card">
        <div class="action-icon">🔗</div>
        <h4>JOIN Report</h4>
        <p>View student participation across organizations and events using multi-table JOIN queries.</p>
        <a href="students_report.php" class="btn btn-primary btn-sm">View Report →</a>
    </div>

    <div class="action-card">
        <div class="action-icon">🔎</div>
        <h4>Subquery Report</h4>
        <p>Lists all organizations that currently have at least one event using a correlated subquery.</p>
        <a href="organization_report.php" class="btn btn-primary btn-sm">View Report →</a>
    </div>

    <div class="action-card">
        <div class="action-icon">📋</div>
        <h4>CTE Report</h4>
        <p>Displays total attendance records per event using a Common Table Expression (CTE).</p>
        <a href="attendance_report.php" class="btn btn-primary btn-sm">View Report →</a>
    </div>

</div>

<!-- Info card -->
<div class="card card-padded" style="max-width:600px; margin-top:8px;">
    <div style="display:flex; gap:14px; align-items:flex-start;">
        <span style="font-size:28px;">💡</span>
        <div>
            <div style="font-family:var(--font-head); font-weight:700; font-size:14px; margin-bottom:4px;">About These Reports</div>
            <p style="font-size:13px; color:var(--text-secondary); line-height:1.6;">
                Each report showcases a different SQL technique — JOINs combine related tables,
                Subqueries filter based on derived conditions, and CTEs create readable named
                intermediate result sets for complex aggregations.
            </p>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
