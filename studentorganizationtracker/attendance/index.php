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

// Summary counts
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
    <a href="mark.php" class="btn btn-primary">+ Mark Attendance</a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert success">✅ <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert error">⚠️ <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
<?php endif; ?>

<!-- Summary Mini Cards -->
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:22px;">
    <div class="card card-padded" style="text-align:center; padding:16px;">
        <div style="font-size:22px; font-weight:800; color:var(--text-primary);"><?php echo $total; ?></div>
        <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">Total Records</div>
    </div>
    <div class="card card-padded" style="text-align:center; padding:16px; border-top:3px solid var(--success);">
        <div style="font-size:22px; font-weight:800; color:var(--success);"><?php echo $present; ?></div>
        <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">Present</div>
    </div>
    <div class="card card-padded" style="text-align:center; padding:16px; border-top:3px solid var(--danger);">
        <div style="font-size:22px; font-weight:800; color:var(--danger);"><?php echo $absent; ?></div>
        <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">Absent</div>
    </div>
    <div class="card card-padded" style="text-align:center; padding:16px; border-top:3px solid var(--warning);">
        <div style="font-size:22px; font-weight:800; color:var(--warning);"><?php echo $late; ?></div>
        <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">Late</div>
    </div>
</div>

<div class="toolbar">
    <div class="toolbar-left">
        <form method="GET" style="display:flex; gap:8px; align-items:center;">
            <div class="search-field">
                <span class="search-icon-inner">🔍</span>
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
    <div style="font-size:12.5px; color:var(--text-muted);">
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
                            <div class="empty-icon">✅</div>
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
                    'present' => ['badge-green',  '✅ Present'],
                    'absent'  => ['badge-red',    '❌ Absent'],
                    'late'    => ['badge-amber',   '⏰ Late'],
                    default   => ['badge-gray',    ucfirst($status)],
                };
                ?>
                <tr>
                    <td class="cell-id">#<?php echo htmlspecialchars($a['attendance_id']); ?></td>
                    <td class="cell-name">🎒 <?php echo htmlspecialchars($a['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($a['event_name']); ?></td>
                    <td><span class="badge badge-blue"><?php echo htmlspecialchars($a['org_name']); ?></span></td>
                    <td><span class="badge <?php echo $status_badge[0]; ?>"><?php echo $status_badge[1]; ?></span></td>
                    <td style="color:var(--text-muted);"><?php echo htmlspecialchars($a['date_marked'] ?? '—'); ?></td>
                    <td>
                        <div class="td-actions">
                            <a href="delete.php?id=<?php echo $a['attendance_id']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete this attendance record?')">🗑️ Delete</a>
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
