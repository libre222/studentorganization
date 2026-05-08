<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$id = $_GET['id'] ?? 0;
$conn = getDBConnection();

$orgs = $conn->query("SELECT * FROM organizations ORDER BY org_name ASC");
$org_list = $orgs ? $orgs->fetch_all(MYSQLI_ASSOC) : [];

$stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    $_SESSION['error'] = 'Event not found.';
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $org_id   = $_POST['org_id'];
    $name     = trim($_POST['event_name']);
    $date     = $_POST['event_date'];
    $location = trim($_POST['location']);

    $stmt = $conn->prepare("UPDATE events SET org_id=?, event_name=?, event_date=?, location=? WHERE event_id=?");
    $stmt->bind_param("isssi", $org_id, $name, $date, $location, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Event updated successfully!';
        header('Location: index.php');
        exit;
    } else {
        $error = 'Update failed.';
    }
    $stmt->close();
}
$conn->close();
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <div>
        <h2>Edit Event</h2>
        <p>Update event details below.</p>
    </div>
    <a href="index.php" class="btn btn-secondary">
        <span class="material-symbols-outlined">arrow_back</span> Back to Events
    </a>
</div>

<?php if ($error): ?>
    <div class="alert error"><span class="material-symbols-outlined">error</span> <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="card" style="max-width: 680px;">
    <div class="card-header">
        <h3>Event Details</h3>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="form-grid">

                <div class="form-group full-width">
                    <label>Organization <span class="req">*</span></label>
                    <select name="org_id" required>
                        <?php foreach ($org_list as $o): ?>
                            <option value="<?php echo $o['org_id']; ?>"
                                <?php echo ($o['org_id'] == $data['org_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($o['org_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label>Event Name <span class="req">*</span></label>
                    <input type="text" name="event_name" value="<?php echo htmlspecialchars($data['event_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Event Date <span class="req">*</span></label>
                    <input type="date" name="event_date" value="<?php echo htmlspecialchars($data['event_date']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" value="<?php echo htmlspecialchars($data['location']); ?>">
                </div>

            </div>

            <div class="form-actions" style="margin-top:24px;">
                <button type="submit" class="btn btn-primary">
                    <span class="material-symbols-outlined">save</span> Save Changes
                </button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
