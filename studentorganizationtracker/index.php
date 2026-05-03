<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

$conn = getDBConnection();

$students   = $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()['total'];
$orgs       = $conn->query("SELECT COUNT(*) AS total FROM organizations")->fetch_assoc()['total'];
$events     = $conn->query("SELECT COUNT(*) AS total FROM events")->fetch_assoc()['total'];
$attendance = $conn->query("SELECT COUNT(*) AS total FROM attendance")->fetch_assoc()['total'];

$conn->close();
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h2>Dashboard Overview</h2>
        <p>Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> — here's what's happening today.</p>
    </div>
    <div class="page-header-actions">
        <span style="font-size:12px; color:var(--text-muted);">
            📅 <?php echo date('F j, Y'); ?>
        </span>
    </div>
</div>

<!-- ── STAT CARDS ───────────────────────── -->
<div class="stat-grid">

    <div class="stat-card">
        <div class="stat-icon blue"></div>
        <div class="stat-value"><?php echo number_format($students); ?></div>
        <div class="stat-label">Total Students</div>
        <div class="stat-trend up">↑ Enrolled</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green"></div>
        <div class="stat-value"><?php echo number_format($orgs); ?></div>
        <div class="stat-label">Organizations</div>
        <div class="stat-trend up">↑ Active</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon amber"></div>
        <div class="stat-value"><?php echo number_format($events); ?></div>
        <div class="stat-label">Total Events</div>
        <div class="stat-trend up">↑ Scheduled</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon cyan"></div>
        <div class="stat-value"><?php echo number_format($attendance); ?></div>
        <div class="stat-label">Attendance Records</div>
        <div class="stat-trend up">↑ Recorded</div>
    </div>

</div>

<!-- ── QUICK ACTIONS ────────────────────── -->
<div class="section-label">Quick Actions</div>

<div class="action-grid">

    <div class="action-card">
        <div class="action-icon"></div>
        <h4>Add Student</h4>
        <p>Register a new student into the system.</p>
        <a href="students/add.php" class="btn btn-primary btn-sm">+ Add Student</a>
    </div>

    <div class="action-card">
        <div class="action-icon"></div>
        <h4>Add Organization</h4>
        <p>Create a new student organization.</p>
        <a href="organizations/add.php" class="btn btn-primary btn-sm">+ Add Org</a>
    </div>

    <div class="action-card">
        <div class="action-icon"></div>
        <h4>Add Event</h4>
        <p>Schedule a new organization event.</p>
        <a href="events/add.php" class="btn btn-primary btn-sm">+ Add Event</a>
    </div>

    <div class="action-card">
        <div class="action-icon"></div>
        <h4>View Reports</h4>
        <p>Analyze participation and attendance data.</p>
        <a href="reports/index.php" class="btn btn-secondary btn-sm">View Reports →</a>
    </div>

</div>

<!-- ── RECENT STUDENTS (preview) ───────── -->
<?php
$conn = getDBConnection();
$recent = $conn->query("SELECT * FROM students ORDER BY student_id DESC LIMIT 5");
$recent_students = $recent ? $recent->fetch_all(MYSQLI_ASSOC) : [];
$conn->close();
?>

<?php if (!empty($recent_students)): ?>
<div class="section-label" style="margin-top: 8px;">Recent Students</div>

<div class="card">
    <div class="card-header">
        <h3>Latest Enrollments</h3>
        <a href="students/index.php" class="btn btn-secondary btn-sm">View All →</a>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_students as $s): ?>
                <tr>
                    <td class="cell-id">#<?php echo htmlspecialchars($s['student_id']); ?></td>
                    <td class="cell-name"><?php echo htmlspecialchars($s['student_name'] ?? ($s['first_name'] . ' ' . $s['last_name'])); ?></td>
                    <td><?php echo htmlspecialchars($s['course'] ?? $s['major'] ?? '—'); ?></td>
                    <td><span class="badge badge-blue"><?php echo htmlspecialchars($s['year_level'] ?? $s['year'] ?? '—'); ?></span></td>
                    <td style="color:var(--text-muted);"><?php echo htmlspecialchars($s['email']); ?></td>
                    <td>
                        <div class="td-actions">
                            <a href="students/edit.php?id=<?php echo $s['student_id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
