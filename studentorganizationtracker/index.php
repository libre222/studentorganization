<?php 
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

$conn = getDBConnection();

// COUNTS (AGGREGATION)
$students = $conn->query("SELECT COUNT(*) as total FROM students")->fetch_assoc()['total'];
$orgs = $conn->query("SELECT COUNT(*) as total FROM organizations")->fetch_assoc()['total'];
$events = $conn->query("SELECT COUNT(*) as total FROM events")->fetch_assoc()['total'];
$attendance = $conn->query("SELECT COUNT(*) as total FROM attendance")->fetch_assoc()['total'];

$conn->close();
?>

<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/navbar.php'; ?>

<div class="container">

    <h2>Dashboard Overview</h2>
    <p style="margin-bottom:20px; color:#666;">
        Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋
    </p>

    <!-- DASHBOARD CARDS -->
    <div class="dashboard-cards">

        <div class="card">
            <h3>Total Students</h3>
            <p><?php echo $students; ?></p>
        </div>

        <div class="card">
            <h3>Total Organizations</h3>
            <p><?php echo $orgs; ?></p>
        </div>

        <div class="card">
            <h3>Total Events</h3>
            <p><?php echo $events; ?></p>
        </div>

        <div class="card">
            <h3>Total Attendance</h3>
            <p><?php echo $attendance; ?></p>
        </div>

    </div>

    <!-- QUICK ACTIONS -->
    <h3 style="margin-top:40px;">Quick Actions</h3>

    <div class="dashboard-cards">

        <div class="card">
            <h3>Add Student</h3>
            <p>Create a new student record.</p>
            <a href="students/add.php" class="btn btn-primary">Add</a>
        </div>

        <div class="card">
            <h3>Add Organization</h3>
            <p>Create a new organization.</p>
            <a href="organizations/add.php" class="btn btn-primary">Add</a>
        </div>

        <div class="card">
            <h3>Add Event</h3>
            <p>Create a new event.</p>
            <a href="events/add.php" class="btn btn-primary">Add</a>
        </div>

        <div class="card">
            <h3>View Reports</h3>
            <p>Check system reports and analytics.</p>
            <a href="reports/index.php" class="btn btn-primary">View</a>
        </div>

    </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
