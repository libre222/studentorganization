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
        SELECT * FROM students
        WHERE student_name LIKE ? OR course LIKE ? OR email LIKE ?
        ORDER BY student_name ASC
    ");
    $like = "%$search%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM students ORDER BY student_name ASC");
}

$students = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$conn->close();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="page-header">
    <div>
        <h2>Students</h2>
        <p>Manage all enrolled student records.</p>
    </div>
    <a href="add.php" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Add Student
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
                       placeholder="Search by name, course, email..."
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Search</button>
            <?php if ($search): ?>
                <a href="index.php" class="btn btn-secondary btn-sm">Clear</a>
            <?php endif; ?>
        </form>
    </div>
    <div style="font-size:13px; color:var(--secondary);">
        <?php echo count($students); ?> record<?php echo count($students) !== 1 ? 's' : ''; ?> found
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Course</th>
                    <th>Year Level</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($students)): ?>
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon"><span class="material-symbols-outlined">school</span></div>
                            <p>No students found<?php echo $search ? ' matching "' . htmlspecialchars($search) . '"' : ''; ?>.</p>
                            <a href="add.php" class="btn btn-primary btn-sm">Add First Student</a>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($students as $s): ?>
                <tr>
                    <td class="cell-id">#<?php echo htmlspecialchars($s['student_id']); ?></td>
                    <td class="cell-name"><?php echo htmlspecialchars($s['student_name'] ?? ($s['first_name'] . ' ' . $s['last_name'])); ?></td>
                    <td style="color:var(--secondary);"><?php echo htmlspecialchars($s['course'] ?? $s['major'] ?? '—'); ?></td>
                    <td><span class="badge badge-blue"><?php echo htmlspecialchars($s['year_level'] ?? $s['year'] ?? '—'); ?></span></td>
                    <td style="color:var(--secondary); font-size:13px;"><?php echo htmlspecialchars($s['email']); ?></td>
                    <td>
                        <div class="td-actions">
                            <a href="edit.php?id=<?php echo $s['student_id']; ?>" class="btn btn-secondary btn-sm">
                                <span class="material-symbols-outlined"></span> Edit
                            </a>
                            <a href="delete.php?id=<?php echo $s['student_id']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete this student? This cannot be undone.')">
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
