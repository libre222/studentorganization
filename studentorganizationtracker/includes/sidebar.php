<?php
// Determine active page for nav highlighting
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir  = basename(dirname($_SERVER['PHP_SELF']));

function nav_active(string $dir, string $file = 'index.php'): string {
    global $current_dir, $current_page;
    // Root dashboard
    if ($dir === 'root' && $current_page === 'index.php' && $current_dir === 'studentorganizationtracker') {
        return 'active';
    }
    return ($current_dir === $dir) ? 'active' : '';
}

// First letter of username for avatar
$uname  = $_SESSION['username'] ?? 'User';
$avatar = strtoupper(substr($uname, 0, 1));
$role   = $_SESSION['role'] ?? 'staff';
?>

<aside class="sidebar">

    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="brand-icon">🎓</div>
        <div class="brand-name">Org Tracker</div>
        <div class="brand-sub">Admin Panel</div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">

        <div class="nav-section-label">Main Menu</div>

        <a href="/studentorganizationtracker/index.php"
           class="nav-item <?php echo nav_active('root'); ?>">
            <span class="nav-icon">⊞</span>
            Dashboard
        </a>

        <a href="/studentorganizationtracker/students/index.php"
           class="nav-item <?php echo nav_active('students'); ?>">
            <span class="nav-icon">🎒</span>
            Students
        </a>

        <a href="/studentorganizationtracker/organizations/index.php"
           class="nav-item <?php echo nav_active('organizations'); ?>">
            <span class="nav-icon">🏛️</span>
            Organizations
        </a>

        <a href="/studentorganizationtracker/events/index.php"
           class="nav-item <?php echo nav_active('events'); ?>">
            <span class="nav-icon">📅</span>
            Events
        </a>

        <a href="/studentorganizationtracker/memberships/index.php"
           class="nav-item <?php echo nav_active('memberships'); ?>">
            <span class="nav-icon">🤝</span>
            Memberships
        </a>

        <a href="/studentorganizationtracker/attendance/index.php"
           class="nav-item <?php echo nav_active('attendance'); ?>">
            <span class="nav-icon">✅</span>
            Attendance
        </a>

        <div class="nav-section-label" style="margin-top:16px;">Analytics</div>

        <a href="/studentorganizationtracker/reports/index.php"
           class="nav-item <?php echo nav_active('reports'); ?>">
            <span class="nav-icon">📊</span>
            Reports
        </a>

    </nav>

    <!-- User + Logout -->
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="user-avatar"><?php echo htmlspecialchars($avatar); ?></div>
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($uname); ?></div>
                <div class="user-role"><?php echo htmlspecialchars($role); ?></div>
            </div>
        </div>
        <a href="/studentorganizationtracker/auth/logout.php" class="btn-logout">
            <span>🚪</span> Sign Out
        </a>
    </div>

</aside>
