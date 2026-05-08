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
        SELECT e.*, o.org_name FROM events e
        JOIN organizations o ON e.org_id = o.org_id
        WHERE e.event_name LIKE ? OR e.location LIKE ? OR o.org_name LIKE ?
        ORDER BY e.event_date DESC
    ");
    $like = "%$search%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("
        SELECT e.*, o.org_name FROM events e
        JOIN organizations o ON e.org_id = o.org_id
        ORDER BY e.event_date DESC
    ");
}

$events = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$conn->close();

function event_badge_class(string $date): string {
    $ts  = strtotime($date);
    $now = time();
    if ($ts > $now) return 'badge-green';
    if ($ts > $now - 86400 * 7) return 'badge-amber';
    return 'badge-gray';
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="page-header">
    <div>
        <h2>Events</h2>
        <p>All scheduled organization events.</p>
    </div>
    <a href="add.php" class="btn btn-primary">
        <span class="material-symbols-outlined"></span> Add Event
    </a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert success"><span class="material-symbols-outlined">check_circle</span> <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
<?php endif; ?>

<div class="toolbar">
    <div class="toolbar-left">
        <form method="GET" style="display:flex; gap:8px; align-items:center;">
            <div class="search-field">
                <span class="material-symbols-outlined search-icon-inner">search</span>
                <input type="text" name="search"
                       placeholder="Search events, org, location..."
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Search</button>
            <?php if ($search): ?><a href="index.php" class="btn btn-secondary btn-sm">Clear</a><?php endif; ?>
        </form>
    </div>
    <div style="font-size:13px; color:var(--secondary);">
        <?php echo count($events); ?> event<?php echo count($events) !== 1 ? 's' : ''; ?>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Event Name</th>
                    <th>Organization</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($events)): ?>
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon"><span class="material-symbols-outlined">event</span></div>
                            <p>No events found.</p>
                            <a href="add.php" class="btn btn-primary btn-sm">Add First Event</a>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($events as $e): ?>
                <tr>
                    <td class="cell-id">#<?php echo htmlspecialchars($e['event_id']); ?></td>
                    <td class="cell-name"><?php echo htmlspecialchars($e['event_name']); ?></td>
                    <td><span class="badge badge-blue"><?php echo htmlspecialchars($e['org_name']); ?></span></td>
                    <td><span class="badge <?php echo event_badge_class($e['event_date']); ?>"><?php echo htmlspecialchars($e['event_date']); ?></span></td>
                    <td style="color:var(--secondary);">
                        <?php echo $e['location'] ? htmlspecialchars($e['location']) : '<em style="color:var(--outline);">—</em>'; ?>
                    </td>
                    <td>
                        <div class="td-actions">
                            <a href="edit.php?id=<?php echo $e['event_id']; ?>" class="btn btn-secondary btn-sm">
                                <span class="material-symbols-outlined"></span> Edit
                            </a>
                            <a href="delete.php?id=<?php echo $e['event_id']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete this event?')">
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
