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
    $stmt = $conn->prepare("SELECT * FROM organizations WHERE org_name LIKE ? OR description LIKE ? ORDER BY org_name ASC");
    $like = "%$search%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM organizations ORDER BY org_name ASC");
}

$organizations = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$conn->close();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="page-header">
    <div>
        <h2>Organizations</h2>
        <p>Manage all student organizations in the system.</p>
    </div>
    <a href="add.php" class="btn btn-primary">+ Add Organization</a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert success">✅ <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
<?php endif; ?>

<div class="toolbar">
    <div class="toolbar-left">
        <form method="GET" style="display:flex; gap:8px; align-items:center;">
            <div class="search-field">
                <span class="search-icon-inner">🔍</span>
                <input type="text" name="search"
                       placeholder="Search organizations..."
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Search</button>
            <?php if ($search): ?><a href="index.php" class="btn btn-secondary btn-sm">Clear</a><?php endif; ?>
        </form>
    </div>
    <div style="font-size:12.5px; color:var(--text-muted);">
        <?php echo count($organizations); ?> organization<?php echo count($organizations) !== 1 ? 's' : ''; ?>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Organization Name</th>
                    <th>Description</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($organizations)): ?>
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <div class="empty-icon">🏛️</div>
                            <p>No organizations found.</p>
                            <a href="add.php" class="btn btn-primary btn-sm">Add First Organization</a>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($organizations as $o): ?>
                <tr>
                    <td class="cell-id">#<?php echo htmlspecialchars($o['org_id']); ?></td>
                    <td class="cell-name"><?php echo htmlspecialchars($o['org_name']); ?></td>
                    <td style="color:var(--text-secondary); max-width:300px;">
                        <?php
                        $desc = $o['description'] ?? '';
                        echo htmlspecialchars(strlen($desc) > 80 ? substr($desc, 0, 80) . '…' : $desc);
                        ?>
                    </td>
                    <td><span class="badge badge-gray">📅 <?php echo htmlspecialchars($o['date_created']); ?></span></td>
                    <td>
                        <div class="td-actions">
                            <a href="edit.php?id=<?php echo $o['org_id']; ?>" class="btn btn-secondary btn-sm">✏️ Edit</a>
                            <a href="delete.php?id=<?php echo $o['org_id']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete this organization?')">🗑️ Delete</a>
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
