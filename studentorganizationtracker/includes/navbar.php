<?php
$dir_map = [
    'studentorganizationtracker' => 'Dashboard',
    'students'                   => 'Students',
    'organizations'              => 'Organizations',
    'events'                     => 'Events',
    'reports'                    => 'Reports',
    'attendance'                 => 'Attendance',
    'memberships'                => 'Memberships',
    'auth'                       => 'Authentication',
];

$file_map = [
    'add.php'    => 'Add New',
    'edit.php'   => 'Edit',
    'delete.php' => 'Delete',
];

$current_dir  = basename(dirname($_SERVER['PHP_SELF']));
$current_file = basename($_SERVER['PHP_SELF']);

$section_title = $dir_map[$current_dir] ?? ucfirst($current_dir);
$sub_title     = ($current_file !== 'index.php' && isset($file_map[$current_file]))
                 ? $file_map[$current_file] : '';

$uname  = $_SESSION['username'] ?? 'User';
$avatar = strtoupper(substr($uname, 0, 1));

$depth = substr_count($_SERVER['PHP_SELF'], '/') - 2;
$base  = str_repeat('../', $depth);
?>

<header class="navbar">

    <div class="navbar-left">
        <div class="navbar-breadcrumb">
            <span>Home</span>
            <span class="crumb-sep">›</span>
            <span><?php echo htmlspecialchars($section_title); ?></span>
            <?php if ($sub_title): ?>
                <span class="crumb-sep">›</span>
                <span><?php echo htmlspecialchars($sub_title); ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="navbar-right">

        <!-- Global Search -->
        <div class="navbar-search" style="position:relative;">
            <span class="search-icon">🔍</span>
            <input
                type="text"
                id="globalSearch"
                placeholder="Quick search..."
                autocomplete="off"
                onkeydown="handleGlobalSearch(event)"
                oninput="showSearchDropdown(this.value)"
                onblur="setTimeout(() => hideSearchDropdown(), 200)"
            >
            <div id="searchDropdown" style="display:none;position:absolute;top:calc(100% + 6px);right:0;width:280px;background:#fff;border:1px solid #E2E8F0;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.10);overflow:hidden;z-index:999;">
                <div style="padding:8px 14px 4px;font-size:11px;font-weight:700;color:#94A3B8;letter-spacing:0.8px;text-transform:uppercase;">Search in...</div>
                <a id="sd-students" href="#" style="display:flex;align-items:center;gap:10px;padding:10px 14px;font-size:13px;color:#0F172A;text-decoration:none;" onmouseover="this.style.background='#F8FAFF'" onmouseout="this.style.background='transparent'"><span>🎒</span> <span>Students</span></a>
                <a id="sd-orgs"     href="#" style="display:flex;align-items:center;gap:10px;padding:10px 14px;font-size:13px;color:#0F172A;text-decoration:none;" onmouseover="this.style.background='#F8FAFF'" onmouseout="this.style.background='transparent'"><span>🏛️</span> <span>Organizations</span></a>
                <a id="sd-events"   href="#" style="display:flex;align-items:center;gap:10px;padding:10px 14px;font-size:13px;color:#0F172A;text-decoration:none;margin-bottom:4px;" onmouseover="this.style.background='#F8FAFF'" onmouseout="this.style.background='transparent'"><span>📅</span> <span>Events</span></a>
            </div>
        </div>

        <!-- Notifications -->
        <div style="position:relative;">
            <button class="navbar-icon-btn" id="notifBtn" title="Notifications" onclick="toggleNotif()" style="position:relative;">
                🔔
                <span style="position:absolute;top:4px;right:4px;width:8px;height:8px;background:#EF4444;border-radius:50%;border:2px solid #F0F2F8;"></span>
            </button>
            <div id="notifPanel" style="display:none;position:absolute;top:calc(100% + 8px);right:0;width:300px;background:#fff;border:1px solid #E2E8F0;border-radius:14px;box-shadow:0 12px 32px rgba(0,0,0,0.12);overflow:hidden;z-index:999;">
                <div style="padding:14px 16px;border-bottom:1px solid #E2E8F0;display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-family:'Plus Jakarta Sans',sans-serif;font-size:14px;font-weight:700;color:#0F172A;">Notifications</span>
                    <span style="font-size:11px;color:#94A3B8;">Today</span>
                </div>
                <div style="padding:10px 0;">
                    <div style="padding:10px 16px;display:flex;gap:10px;align-items:flex-start;">
                        <span style="font-size:20px;">🎒</span>
                        <div>
                            <div style="font-size:13px;font-weight:600;color:#0F172A;">System Ready</div>
                            <div style="font-size:12px;color:#94A3B8;margin-top:2px;">Student Org Tracker is running.</div>
                        </div>
                    </div>
                    <div style="padding:10px 16px;display:flex;gap:10px;align-items:flex-start;">
                        <span style="font-size:20px;">📅</span>
                        <div>
                            <div style="font-size:13px;font-weight:600;color:#0F172A;">Check your events</div>
                            <div style="font-size:12px;color:#94A3B8;margin-top:2px;">Make sure all events are up to date.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User pill -->
        <div class="navbar-user" title="Logged in as <?php echo htmlspecialchars($uname); ?>">
            <div class="avatar"><?php echo htmlspecialchars($avatar); ?></div>
            <span class="uname"><?php echo htmlspecialchars($uname); ?></span>
        </div>

    </div>
</header>

<script>
const base = '<?php echo $base; ?>';

function handleGlobalSearch(e) {
    if (e.key === 'Enter') {
        const q = document.getElementById('globalSearch').value.trim();
        if (q) window.location.href = base + 'students/index.php?search=' + encodeURIComponent(q);
    }
}

function showSearchDropdown(val) {
    const dd = document.getElementById('searchDropdown');
    if (!val.trim()) { dd.style.display = 'none'; return; }
    const q = encodeURIComponent(val.trim());
    dd.style.display = 'block';
    document.getElementById('sd-students').href = base + 'students/index.php?search='      + q;
    document.getElementById('sd-orgs').href     = base + 'organizations/index.php?search=' + q;
    document.getElementById('sd-events').href   = base + 'events/index.php?search='        + q;
}

function hideSearchDropdown() {
    document.getElementById('searchDropdown').style.display = 'none';
}

function toggleNotif() {
    const p = document.getElementById('notifPanel');
    p.style.display = p.style.display === 'block' ? 'none' : 'block';
}

document.addEventListener('click', function(e) {
    const btn   = document.getElementById('notifBtn');
    const panel = document.getElementById('notifPanel');
    if (btn && panel && !btn.contains(e.target) && !panel.contains(e.target)) {
        panel.style.display = 'none';
    }
});
</script>
