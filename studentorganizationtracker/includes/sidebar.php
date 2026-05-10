<?php
$cur_page = basename($_SERVER['PHP_SELF']);
$cur_dir  = basename(dirname($_SERVER['PHP_SELF']));

function nav_active(string $dir): string {
    global $cur_dir, $cur_page;
    if ($dir === 'root' && $cur_page === 'index.php' && $cur_dir === 'studentorganizationtracker') return 'active';
    return ($cur_dir === $dir) ? 'active' : '';
}

$uname  = $_SESSION['username'] ?? 'User';
$avatar = strtoupper(substr($uname, 0, 1));
$role   = $_SESSION['role'] ?? 'Staff';
?>
<aside class="sidebar">
  <div class="sidebar-brand">
    <div class="brand-logomark">
      <span class="material-symbols-rounded">corporate_fare</span>
    </div>
    <div class="brand-copy">
      <div class="brand-name">OrgTracker</div>
      <div class="brand-tagline">Admin Portal</div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section">Main</div>

    <a href="/studentorganizationtracker/index.php"
       class="nav-link <?php echo nav_active('root'); ?>">
      <span class="material-symbols-rounded">dashboard</span>
      Dashboard
    </a>

    <a href="/studentorganizationtracker/students/index.php"
       class="nav-link <?php echo nav_active('students'); ?>">
      <span class="material-symbols-rounded">group</span>
      Students
    </a>

    <a href="/studentorganizationtracker/organizations/index.php"
       class="nav-link <?php echo nav_active('organizations'); ?>">
      <span class="material-symbols-rounded">account_balance</span>
      Organizations
    </a>

    <a href="/studentorganizationtracker/events/index.php"
       class="nav-link <?php echo nav_active('events'); ?>">
      <span class="material-symbols-rounded">event</span>
      Events
    </a>

    <a href="/studentorganizationtracker/memberships/index.php"
       class="nav-link <?php echo nav_active('memberships'); ?>">
      <span class="material-symbols-rounded">badge</span>
      Memberships
    </a>

    <a href="/studentorganizationtracker/attendance/index.php"
       class="nav-link <?php echo nav_active('attendance'); ?>">
      <span class="material-symbols-rounded">fact_check</span>
      Attendance
    </a>

    <div class="nav-section" style="margin-top:10px;">Analytics</div>

    <a href="/studentorganizationtracker/reports/index.php"
       class="nav-link <?php echo nav_active('reports'); ?>">
      <span class="material-symbols-rounded">bar_chart_4_bars</span>
      Reports
    </a>
  </nav>

  <div class="sidebar-foot">
    <div class="sidebar-user">
      <div class="user-ring"><?php echo htmlspecialchars($avatar); ?></div>
      <div class="user-meta">
        <div class="uname"><?php echo htmlspecialchars($uname); ?></div>
        <div class="urole"><?php echo htmlspecialchars($role); ?></div>
      </div>
    </div>
    <a href="/studentorganizationtracker/auth/logout.php" class="logout-link">
      <span class="material-symbols-rounded">logout</span>
      Sign Out
    </a>
  </div>
</aside>
