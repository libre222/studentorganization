<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$conn   = getDBConnection();
$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $stmt = $conn->prepare("
        SELECT a.attendance_id, s.student_name, e.event_name, o.org_name, a.status, a.date_marked
        FROM attendance a
        JOIN students s ON a.student_id = s.student_id
        JOIN events e ON a.event_id = e.event_id
        JOIN organizations o ON e.org_id = o.org_id
        WHERE s.student_name LIKE ? OR e.event_name LIKE ? OR a.status LIKE ?
        ORDER BY a.attendance_id DESC
    ");
    $like = "%$search%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("
        SELECT a.attendance_id, s.student_name, e.event_name, o.org_name, a.status, a.date_marked
        FROM attendance a
        JOIN students s ON a.student_id = s.student_id
        JOIN events e ON a.event_id = e.event_id
        JOIN organizations o ON e.org_id = o.org_id
        ORDER BY a.attendance_id DESC
    ");
}

$records = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

$total   = count($records);
$present = count(array_filter($records, fn($r) => strtolower($r['status']) === 'present'));
$absent  = count(array_filter($records, fn($r) => strtolower($r['status']) === 'absent'));
$late    = count(array_filter($records, fn($r) => strtolower($r['status']) === 'late'));

$conn->close();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="page-header">
    <div>
        <h2>Attendance</h2>
        <p>Track student attendance across all events.</p>
    </div>
    <a href="mark.php" class="btn btn-primary">
        <span class="material-symbols-outlined"></span> Mark Attendance
    </a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert success"><span class="material-symbols-outlined">check_circle</span> <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert error"><span class="material-symbols-outlined">error</span> <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
<?php endif; ?>

<!-- Summary Cards -->
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:var(--sp-lg); margin-bottom:var(--sp-lg);">
    <div class="stat-card">
        <div class="stat-label">Total Records</div>
        <div class="stat-value" style="color:var(--on-surface);"><?php echo $total; ?></div>
    </div>
    <div class="stat-card" style="border-top:3px solid #22c55e;">
        <div class="stat-label">Present</div>
        <div class="stat-value" style="color:#16a34a;"><?php echo $present; ?></div>
    </div>
    <div class="stat-card" style="border-top:3px solid #ef4444;">
        <div class="stat-label">Absent</div>
        <div class="stat-value" style="color:#dc2626;"><?php echo $absent; ?></div>
    </div>
    <div class="stat-card" style="border-top:3px solid #f59e0b;">
        <div class="stat-label">Late</div>
        <div class="stat-value" style="color:#d97706;"><?php echo $late; ?></div>
    </div>
</div>

<div class="toolbar">
    <div class="toolbar-left">
        <form method="GET" style="display:flex; gap:8px; align-items:center;">
            <div class="search-field">
                <span class="material-symbols-outlined search-icon-inner">search</span>
                <input type="text" name="search"
                       placeholder="Search by student, event, status..."
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Search</button>
            <?php if ($search): ?>
                <a href="index.php" class="btn btn-secondary btn-sm">Clear</a>
            <?php endif; ?>
        </form>
    </div>
    <div style="font-size:13px; color:var(--secondary);">
        <?php echo $total; ?> record<?php echo $total !== 1 ? 's' : ''; ?>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Event</th>
                    <th>Organization</th>
                    <th>Status</th>
                    <th>Date Marked</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($records)): ?>
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <div class="empty-icon"><span class="material-symbols-outlined">how_to_reg</span></div>
                            <p>No attendance records found<?php echo $search ? ' matching "' . htmlspecialchars($search) . '"' : ''; ?>.</p>
                            <a href="mark.php" class="btn btn-primary btn-sm">Mark Attendance</a>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($records as $a): ?>
                <?php
                $status = strtolower($a['status'] ?? 'present');
                $status_badge = match($status) {
                    'present' => ['badge-green', 'Present'],
                    'absent'  => ['badge-red',   'Absent'],
                    'late'    => ['badge-amber',  'Late'],
                    default   => ['badge-gray',   ucfirst($status)],
                };
                ?>
                <tr>
                    <td class="cell-id">#<?php echo htmlspecialchars($a['attendance_id']); ?></td>
                    <td class="cell-name"><?php echo htmlspecialchars($a['student_name']); ?></td>
                    <td style="color:var(--secondary);"><?php echo htmlspecialchars($a['event_name']); ?></td>
                    <td><span class="badge badge-blue"><?php echo htmlspecialchars($a['org_name']); ?></span></td>
                    <td><span class="badge <?php echo $status_badge[0]; ?>"><?php echo $status_badge[1]; ?></span></td>
                    <td style="color:var(--secondary);"><?php echo htmlspecialchars($a['date_marked'] ?? '—'); ?></td>
                    <td>
                        <div class="td-actions">
                            <a href="delete.php?id=<?php echo $a['attendance_id']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete this attendance record?')">
                                <span class="material-symbols-outlined"></span> Delete
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
