<header class="navbar">
    <div class="container">
        <div class="nav-brand">
            <h1>Student Org Tracker</h1>
        </div>
        <nav>
            <a href="../students/index.php">Students</a>
            <a href="../organizations/index.php">Organizations</a>
            <a href="../events/index.php">Events</a>
            <a href="../reports/students_report.php">Reports</a>
            <span class="user-info">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> | 
            <a href="../auth/logout.php">Logout</a></span>
        </nav>
    </div>
</header>

