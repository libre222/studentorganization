<aside class="sidebar">

    <h2 class="logo">Admin Panel</h2>

    <a href="/studentorganizationtracker/index.php">Dashboard</a>
    <a href="/studentorganizationtracker/students/index.php">Students</a>
    <a href="/studentorganizationtracker/organizations/index.php">Organizations</a>
    <a href="/studentorganizationtracker/events/index.php">Events</a>
    <a href="/studentorganizationtracker/reports/index.php">Reports</a>

    <div class="sidebar-footer">
        <p>👤 <?php echo htmlspecialchars($_SESSION['username']); ?></p>
        <a href="/studentorganizationtracker/auth/logout.php">Logout</a>
    </div>

</aside>

<div class="main-content">
