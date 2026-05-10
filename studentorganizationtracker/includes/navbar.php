<?php
$dir_map = [
    'studentorganizationtracker' => 'Dashboard',
    'students'     => 'Students',
    'organizations'=> 'Organizations',
    'events'       => 'Events',
    'reports'      => 'Reports',
    'attendance'   => 'Attendance',
    'memberships'  => 'Memberships',
    'auth'         => 'Authentication',
];
$file_map = ['add.php' => 'Add New', 'edit.php' => 'Edit', 'mark.php' => 'Mark'];

$cur_dir   = basename(dirname($_SERVER['PHP_SELF']));
$cur_file  = basename($_SERVER['PHP_SELF']);
$section   = $dir_map[$cur_dir] ?? ucfirst($cur_dir);
$sub       = ($cur_file !== 'index.php' && isset($file_map[$cur_file])) ? $file_map[$cur_file] : '';

$uname  = $_SESSION['username'] ?? 'User';
$avatar = strtoupper(substr($uname, 0, 1));
$depth  = substr_count($_SERVER['PHP_SELF'], '/') - 2;
$base   = str_repeat('../', $depth);
?>
<header class="topbar">
  <div class="topbar-left">
    <div class="topbar-crumb">
      <span>Home</span>
      <span class="sep">›</span>
      <span class="crumb-cur"><?php echo htmlspecialchars($section); ?></span>
      <?php if ($sub): ?>
        <span class="sep">›</span>
        <span><?php echo htmlspecialchars($sub); ?></span>
      <?php endif; ?>
    </div>
    <div class="topbar-search" style="position:relative;">
      <span class="material-symbols-rounded ico">search</span>
      <input type="text" id="globalSearch" placeholder="Quick search..."
             autocomplete="off"
             onkeydown="handleGS(event)"
             oninput="showSD(this.value)"
             onblur="setTimeout(hideSD, 180)">
      <div id="searchDropdown">
        <div class="sd-label">Search in...</div>
        <a class="sd-item" id="sd-s" href="#">
          <span class="material-symbols-rounded">group</span> Students
        </a>
        <a class="sd-item" id="sd-o" href="#">
          <span class="material-symbols-rounded">account_balance</span> Organizations
        </a>
        <a class="sd-item" id="sd-e" href="#">
          <span class="material-symbols-rounded">event</span> Events
        </a>
      </div>
    </div>
  </div>

  <div class="topbar-right">
    <div style="position:relative;">
      <button class="topbar-btn" id="notifBtn" onclick="toggleNotif()" title="Notifications">
        <span class="material-symbols-rounded">notifications</span>
        <span class="badge-dot"></span>
      </button>
      <div id="notifPanel">
        <div class="notif-head">
          <span>Notifications</span>
          <span>Today</span>
        </div>
        <div class="notif-item">
          <div class="notif-icon"><span class="material-symbols-rounded">check_circle</span></div>
          <div>
            <h6>System is Running</h6>
            <p>Student Org Tracker is operational.</p>
          </div>
        </div>
        <div class="notif-item">
          <div class="notif-icon"><span class="material-symbols-rounded">event</span></div>
          <div>
            <h6>Review Upcoming Events</h6>
            <p>Ensure all events are scheduled correctly.</p>
          </div>
        </div>
      </div>
    </div>

    <button class="topbar-btn" title="Settings">
      <span class="material-symbols-rounded">settings</span>
    </button>

    <div class="topbar-divider"></div>

    <div class="topbar-user">
      <div class="t-utext">
        <div class="t-uname"><?php echo htmlspecialchars($uname); ?></div>
        <div class="t-urole"><?php echo htmlspecialchars($_SESSION['role'] ?? 'Staff'); ?></div>
      </div>
      <div class="t-avatar"><?php echo htmlspecialchars($avatar); ?></div>
    </div>
  </div>
</header>

<script>
const base = '<?php echo $base; ?>';
function handleGS(e) {
  if (e.key === 'Enter') {
    const q = document.getElementById('globalSearch').value.trim();
    if (q) location.href = base + 'students/index.php?search=' + encodeURIComponent(q);
  }
}
function showSD(v) {
  const d = document.getElementById('searchDropdown');
  if (!v.trim()) { d.style.display = 'none'; return; }
  const q = encodeURIComponent(v.trim());
  d.style.display = 'block';
  document.getElementById('sd-s').href = base + 'students/index.php?search=' + q;
  document.getElementById('sd-o').href = base + 'organizations/index.php?search=' + q;
  document.getElementById('sd-e').href = base + 'events/index.php?search=' + q;
}
function hideSD() {
  document.getElementById('searchDropdown').style.display = 'none';
}
function toggleNotif() {
  const p = document.getElementById('notifPanel');
  p.style.display = p.style.display === 'block' ? 'none' : 'block';
}
document.addEventListener('click', e => {
  const btn = document.getElementById('notifBtn');
  const pan = document.getElementById('notifPanel');
  if (btn && pan && !btn.contains(e.target) && !pan.contains(e.target))
    pan.style.display = 'none';
});
</script>
