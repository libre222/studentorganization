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
        SELECT m.membership_id, s.student_name, o.org_name, m.date_joined, m.role
        FROM memberships m
        JOIN students s ON m.student_id = s.student_id
        JOIN organizations o ON m.org_id = o.org_id
        WHERE s.student_name LIKE ? OR o.org_name LIKE ? OR m.role LIKE ?
        ORDER BY m.membership_id DESC
    ");
    $like = "%$search%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("
        SELECT m.membership_id, s.student_name, o.org_name, m.date_joined, m.role
        FROM memberships m
        JOIN students s ON m.student_id = s.student_id
        JOIN organizations o ON m.org_id = o.org_id
        ORDER BY m.membership_id DESC
    ");
}

$memberships = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$conn->close();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="page-header">
    <div>
        <h2>Memberships</h2>
        <p>Manage student memberships in organizations.</p>
    </div>
    <a href="add.php" class="btn btn-primary">
        <span class="material-symbols-outlined"></span> Add Membership
    </a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert success"><span class="material-symbols-outlined">check_circle</span> <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert error"><span class="material-symbols-outlined">error</span> <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="toolbar">
    <div class="toolbar-left">
        <form method="GET" style="display:flex; gap:8px; align-items:center;">
            <div class="search-field">
                <span class="material-symbols-outlined search-icon-inner">search</span>
                <input type="text" name="search"
                       placeholder="Search by student, org, role..."
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Search</button>
            <?php if ($search): ?>
                <a href="index.php" class="btn btn-secondary btn-sm">Clear</a>
            <?php endif; ?>
        </form>
    </div>
    <div style="font-size:13px; color:var(--secondary);">
        <?php echo count($memberships); ?> record<?php echo count($memberships) !== 1 ? 's' : ''; ?>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Organization</th>
                    <th>Role</th>
                    <th>Date Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($memberships)): ?>
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon"><span class="material-symbols-outlined">card_membership</span></div>
                            <p>No memberships found<?php echo $search ? ' matching "' . htmlspecialchars($search) . '"' : ''; ?>.</p>
                            <a href="add.php" class="btn btn-primary btn-sm">Add First Membership</a>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($memberships as $m): ?>
                <tr>
                    <td class="cell-id">#<?php echo htmlspecialchars($m['membership_id']); ?></td>
                    <td class="cell-name"><?php echo htmlspecialchars($m['student_name']); ?></td>
                    <td><span class="badge badge-blue"><?php echo htmlspecialchars($m['org_name']); ?></span></td>
                    <td>
                        <?php
                        $role = $m['role'] ?? 'Member';
                        $badge_class = match(strtolower($role)) {
                            'president'  => 'badge-amber',
                            'officer'    => 'badge-green',
                            'secretary'  => 'badge-cyan',
                            default      => 'badge-gray',
                        };
                        ?>
                        <span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($role); ?></span>
                    </td>
                    <td style="color:var(--secondary);"><?php echo htmlspecialchars($m['date_joined'] ?? '—'); ?></td>
                    <td>
                        <div class="td-actions">
                            <a href="delete.php?id=<?php echo $m['membership_id']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Remove this membership?')">
                                <span class="material-symbols-outlined"></span> Remove
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
