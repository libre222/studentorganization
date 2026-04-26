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
        SELECT * FROM organizations 
        WHERE org_name LIKE ? 
        OR description LIKE ?
        ORDER BY org_name ASC
    ");
    
    $like = "%$search%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM organizations ORDER BY org_name ASC");
}

$organizations = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">

<h2>Organizations</h2>

<!-- SEARCH BAR -->
<form method="GET" class="search-bar">
    <input 
        type="text" 
        name="search" 
        placeholder="Search organizations..." 
        value="<?php echo htmlspecialchars($search); ?>"
    >
    <button type="submit" class="btn btn-primary">Search</button>
    <a href="index.php" class="btn btn-secondary">Reset</a>
</form>

<a href="add.php" class="btn btn-primary">Add Organization</a>

<br><br>

<table class="data-table">
<tr>
    <th>ID</th>
    <th>Organization Name</th>
    <th>Description</th>
    <th>Date Created</th>
    <th>Action</th>
</tr>

<?php if (empty($organizations)): ?>
<tr>
    <td colspan="5">No organizations found.</td>
</tr>
<?php else: ?>

<?php foreach ($organizations as $o): ?>
<tr>
    <td><?php echo htmlspecialchars($o['org_id']); ?></td>
    <td><?php echo htmlspecialchars($o['org_name']); ?></td>
    <td><?php echo htmlspecialchars($o['description']); ?></td>
    <td><?php echo htmlspecialchars($o['date_created']); ?></td>
    <td>
        <a href="edit.php?id=<?php echo $o['org_id']; ?>" class="btn btn-small btn-secondary">Edit</a>
        <a href="delete.php?id=<?php echo $o['org_id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Delete this organization?')">Delete</a>
    </td>
</tr>
<?php endforeach; ?>

<?php endif; ?>

</table>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
