<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$conn = getDBConnection();

// SEARCH
$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $stmt = $conn->prepare("
        SELECT e.*, o.org_name 
        FROM events e
        JOIN organizations o ON e.org_id = o.org_id
        WHERE e.event_name LIKE ? 
        OR e.location LIKE ?
        OR o.org_name LIKE ?
        ORDER BY e.event_date DESC
    ");
    
    $like = "%$search%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("
        SELECT e.*, o.org_name 
        FROM events e
        JOIN organizations o ON e.org_id = o.org_id
        ORDER BY e.event_date DESC
    ");
}

$events = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">

<h2>Events</h2>

<!-- SEARCH BAR -->
<form method="GET" class="search-bar">
    <input 
        type="text" 
        name="search" 
        placeholder="Search events..." 
        value="<?php echo htmlspecialchars($search); ?>"
    >
    <button type="submit" class="btn btn-primary">Search</button>
    <a href="index.php" class="btn btn-secondary">Reset</a>
</form>

<a href="add.php" class="btn btn-primary">Add Event</a>

<br><br>

<table class="data-table">
<tr>
    <th>ID</th>
    <th>Event Name</th>
    <th>Organization</th>
    <th>Date</th>
    <th>Location</th>
    <th>Action</th>
</tr>

<?php if (empty($events)): ?>
<tr>
    <td colspan="6">No events found.</td>
</tr>
<?php else: ?>

<?php foreach ($events as $e): ?>
<tr>
    <td><?php echo htmlspecialchars($e['event_id']); ?></td>
    <td><?php echo htmlspecialchars($e['event_name']); ?></td>
    <td><?php echo htmlspecialchars($e['org_name']); ?></td>
    <td><?php echo htmlspecialchars($e['event_date']); ?></td>
    <td><?php echo htmlspecialchars($e['location']); ?></td>
    <td>
        <a href="edit.php?id=<?php echo $e['event_id']; ?>" class="btn btn-small btn-secondary">Edit</a>
        <a href="delete.php?id=<?php echo $e['event_id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Delete this event?')">Delete</a>
    </td>
</tr>
<?php endforeach; ?>

<?php endif; ?>

</table>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
