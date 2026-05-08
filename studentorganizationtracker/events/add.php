<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$conn = getDBConnection();
$orgs = $conn->query("SELECT * FROM organizations ORDER BY org_name ASC");
$org_list = $orgs ? $orgs->fetch_all(MYSQLI_ASSOC) : [];

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $org_id   = $_POST['org_id'];
    $name     = trim($_POST['event_name']);
    $date     = $_POST['event_date'];
    $location = trim($_POST['location']);

    if (empty($org_id) || empty($name) || empty($date)) {
        $error = 'Please fill in all required fields.';
    } else {
        $stmt = $conn->prepare("INSERT INTO events (org_id, event_name, event_date, location) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $org_id, $name, $date, $location);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Event added successfully!';
            header('Location: index.php');
            exit;
        } else {
            $error = 'Failed to add event.';
        }
        $stmt->close();
    }
}
$conn->close();
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <div>
        <h2>Add Event</h2>
        <p>Schedule a new organization event.</p>
    </div>
    <a href="index.php" class="btn btn-secondary">
        <span class="material-symbols-outlined"></span> Back to Events
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
                        <option value="">— Select Organization —</option>
                        <?php foreach ($org_list as $o): ?>
                            <option value="<?php echo $o['org_id']; ?>"
                                <?php echo (($_POST['org_id'] ?? '') == $o['org_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($o['org_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label>Event Name <span class="req">*</span></label>
                    <input type="text" name="event_name"
                           placeholder="e.g. General Assembly 2025"
                           value="<?php echo htmlspecialchars($_POST['event_name'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label>Event Date <span class="req">*</span></label>
                    <input type="date" name="event_date"
                           value="<?php echo htmlspecialchars($_POST['event_date'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location"
                           placeholder="e.g. University Gymnasium"
                           value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>">
                </div>

            </div>

            <div class="form-actions" style="margin-top:24px;">
                <button type="submit" class="btn btn-primary">
                    <span class="material-symbols-outlined"></span> Add Event
                </button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
